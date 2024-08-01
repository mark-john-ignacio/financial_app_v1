<?php

namespace App\Controllers\WooCommerceOrderSync;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WooCommerceOrderSync\SalesOrderModel;
use App\Models\WooCommerceOrderSync\CustomersModel;
use App\Models\WooCommerceOrderSync\SalesOrderItemsModel;
use App\Models\WooCommerceOrderSync\ItemsModel;
use CodeIgniter\RESTful\ResourceController;

class OrderController extends BaseController
{
    protected $salesOrderModel;

    public function __construct()
    {
        $this->salesOrderModel = new SalesOrderModel();
        $this->customersModel = new CustomersModel();
        $this->salesOrderItemsModel = new SalesOrderItemsModel();
        $this->itemsModel = new ItemsModel();
        $this->company_code = "001";
    }
    public function receiveOrder(){

        // Being able to receive the webhook verification request
        $webhookId = $this->request->getPost('webhook_id');

        if ($webhookId){
            return $this->response->setJSON(['message' => 'This is a webhook verification request']);
        }

        // Verify the webhook signature
        $jsonData = $this->request->getJSON(true);
        $webhookSecret = getenv('WEBHOOK_SECRET');
        $webhookSignature = $this->request->getHeaderLine('x-wc-webhook-signature');
        $computedSignature = base64_encode(hash_hmac('sha256', json_encode($jsonData), $webhookSecret, true));
        
        if (!hash_equals($webhookSignature, $computedSignature)) {
            return $this->response->setJSON(['message' => 'Invalid signature']);
        }

        // Log the webhook data
        $this->logWebhookData($jsonData);
        
        // Insert the order data into the database
        $customerCode = $this->getCustomerCode($jsonData);
        $ctranno = $this->salesOrderModel->generateSONumber($this->company_code);
        $data = [
            'compcode' => $this->company_code,
            'ctranno' => $ctranno,
            'ccode' => $customerCode,
            'ddate' => $jsonData['date_created'],
            'dcutdate' => $jsonData['date_created'],
            'dpodate' => $jsonData['date_created'],
            'csalestype' => 'Goods',
            'cpono' => $jsonData['id'],
            'ngross' => $jsonData['total'],
            'nbasegross' => $jsonData['total'],
            'ccurrencycode' => $jsonData['currency'],
            'ccurrencydesc' => $jsonData['currency'],
            'nexchangerate' => 1,
            'cremarks' => $jsonData['customer_note'],
            'cpreparedby' => 'WooCommerce',
            'csalesman' => 'WooCommerce',
            'cdelcode' => $customerCode,
            'cdeladdno' => $jsonData['shipping']['address_1'] . ' ' . $jsonData['shipping']['address_2'],
            'cdeladdcity' => $jsonData['shipping']['city'],
            'cdeladdstate' => $jsonData['shipping']['state'],
            'cdeladdcountry' => $jsonData['shipping']['country'],
            'cdeladdzip' => $jsonData['shipping']['postcode'],
        ];

        $result = $this->salesOrderModel->insert($data);

        if($result){
            $this->insertSalesOrderItems($jsonData, $ctranno);
            return $this->response->setJSON(['message' => 'Order received']);
        }else{
            $error = $result;
            return $this->response->setJSON(['message' => $error]);
        }
    }

    private function getCustomerCode($jsonData){
        $customerName = strtoupper($jsonData['billing']['first_name'] . ' ' . $jsonData['billing']['last_name']);
        $customer = $this->customersModel->where('compcode', $this->company_code)->where('cname', $customerName)->first();
        if ($customer){
            return $customer->cempid;
        }else{
            $data = [
                'compcode' => $this->company_code,
                'cempid' => $this->generateCustomerCode(),
                'cname' => $customerName,
                'ctradename' => strtoupper($jsonData['billing']['company']),
                'chouseno' => $jsonData['billing']['address_1'],
                'ccity' => $jsonData['billing']['city'],
                'cstate' => $jsonData['billing']['state'],
                'ccountry' => $jsonData['billing']['country'],
                'czip' => $jsonData['billing']['postcode'],
            ];
            $this->customersModel->insert($data);
            return $data['cempid'];
        }
    }

    private function generateCustomerCode(){
        // Fetch the highest customer code from the database
        $highestCode = $this->customersModel
                            ->where('compcode', $this->company_code)
                            ->where('cempid LIKE', 'CUSTWC_%')
                            ->orderBy('cempid', 'desc')
                            ->first();
    
        if ($highestCode) {
            // Extract the numeric part of the highest code
            $numberPart = intval(substr($highestCode->cempid, 8));
        } else {
            // If no codes exist, start from 0
            $numberPart = 0;
        }
    
        // Increment the number to get the next unique number
        $newNumber = $numberPart + 1;
    
        // Determine the padding length dynamically based on the highest number part
        $paddingLength = max(3, strlen((string)$numberPart));
    
        // Format the new number to maintain leading zeros
        $formattedNumber = str_pad($newNumber, $paddingLength, '0', STR_PAD_LEFT);
    
        // Generate the new customer code
        $newCustomerCode = "CUSTWC_" . $formattedNumber;
    
        return $newCustomerCode;
    }

    private function insertSalesOrderItems($jsonData, $salesOrderId){
        $items = $jsonData['line_items'];
        foreach ($items as $item){
            $transformedName = $this->transformProductName($item['name']);
            $product = $this->itemsModel->where('compcode', $this->company_code)->where('citemdesc', $transformedName)->first();
            if (!$product){
                $data = [
                    'compcode' => $this->company_code,
                    'cpartno' => $this->generateItemPartNo(),
                    'cskucode' => $item['sku'],
                    'citemdesc' => $item['name'],
                    'cunit' => 'PCS',
                    'cclass' => 'Goods',
                    'ctype' => 'ITEM',
                    'csalestype' => 'Goods',
                    'ctradetype' => 'Goods',
                    'ctaxcode' => 'VT',
                    'cpricetype' => 'MU',
                    'nmarkup' => 0,
                    'ccacctcodesales' => '90',


                ];
                $this->itemsModel->insert($data);
                $product = $this->itemsModel->find($this->itemsModel->insertID());
                $this->prepareAndInsertIntoSalesOrderItems($salesOrderId, $item, $product);

            }else{
                $this->prepareAndInsertIntoSalesOrderItems($salesOrderId, $item, $product);
            }
        }
    }

    private function transformProductName($name){
        $name = strtoupper($name);
        $name = str_replace(' - SMALL', ' S', $name);
        $name = str_replace(' - MEDIUM', ' M', $name);
        $name = str_replace(' - LARGE', ' L', $name);
        $name = str_replace(' - EXTRA LARGE', ' XL', $name);

        return $name;
    }

    private function generateItemPartNo(){
        $prefix = 'ITEM';
        $column = 'cpartno';
        
        // Fetch the highest customer code from the database
        $highestCode = $this->itemsModel
                            ->where('compcode', $this->company_code)
                            ->where($column . ' LIKE', $prefix . '%')
                            ->orderBy($column, 'desc')
                            ->first();
    
        if ($highestCode) {
            // Extract the numeric part of the highest code
            $numberPart = intval(substr($highestCode->cpartno, 4));
        } else {
            // If no codes exist, start from 0
            $numberPart = 0;
        }
    
        // Increment the number to get the next unique number
        $newNumber = $numberPart + 1;
    
        // Determine the padding length dynamically based on the highest number part
        $paddingLength = max(3, strlen((string)$numberPart));
    
        // Format the new number to maintain leading zeros
        $formattedNumber = str_pad($newNumber, $paddingLength, '0', STR_PAD_LEFT);
    
        // Generate the new customer code
        $newCustomerCode = $prefix . $formattedNumber;
    
        return $newCustomerCode;

    }

    private function prepareAndInsertIntoSalesOrderItems($salesOrderId, $item, $product){
        $cidentity = $this->generateSOTCidentity($salesOrderId);
        $nident = intval(substr($cidentity, strrpos($cidentity, 'P') + 1));

        $data = [
            'compcode' => $this->company_code,
            'cidentity' => $cidentity,
            'ctranno' => $salesOrderId,
            'creference' => $product->cpartno,
            'nident' => $nident,
            'nrefident' => $nident,
            'citemno' => $product->cpartno,
            'nqty' => $item['quantity'],
            'cunit' => $product->cunit,
            'nprice' => $item['total'],
            'namount' => $item['price'],
            'nbaseamount' => $item['price'],
            'cmainunit' => $product->cunit,
            'nfactor' => 1,
            'nbase' => 0,
            'ndisc' => 0,
            'nnet' => 0,
            'ctaxcode' => 'NT',
            'nrate' => 0,
        ];
        $this->salesOrderItemsModel->insert($data);
    }

    private function generateSOTCidentity($salesOrderId){

        $pattern = $salesOrderId . 'P%';
        $latestItem = $this->salesOrderItemsModel
                           ->like('cidentity', $pattern, 'after')
                           ->orderBy('cidentity', 'desc')
                           ->first();
    
        $nextNumber = 0; // Default if no previous items are found
        if ($latestItem) {

            $currentNumber = substr($latestItem->cidentity, strrpos($latestItem->cidentity, 'P') + 1);

            $nextNumber = intval($currentNumber) + 1;
        }
    

        $newCidentity = $salesOrderId . 'P' . $nextNumber;
    
        return $newCidentity;
    }

    private function logWebhookData($jsonData)
    {
        $logFilePath = WRITEPATH . 'logs/webhook_log_' . date('Y-m-d') . '.log';
        $logMessage = '[' . date('Y-m-d H:i:s') . '] Webhook Received: ' . json_encode($jsonData) . PHP_EOL;

        // Check if the log file exists and is writable, or if it does not exist, attempt to create it.
        if (is_writable($logFilePath) || !file_exists($logFilePath) && is_writable(dirname($logFilePath))) {
            file_put_contents($logFilePath, $logMessage, FILE_APPEND);
        } else {
            // Optionally, handle the error in case the log file is not writable or cannot be created.
            error_log('Failed to write webhook data to log file.');
        }
    }
}