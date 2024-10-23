<?php

namespace App\Controllers\WooCommerceOrderSync;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WooCommerceOrderSync\SalesOrderModel;
use App\Models\WooCommerceOrderSync\CustomersModel;
use App\Models\WooCommerceOrderSync\SalesOrderItemsModel;
use App\Models\WooCommerceOrderSync\ItemsModel;
use App\Models\WooCommerceOrderSync\LandingOrderTable;
use CodeIgniter\RESTful\ResourceController;

class OrderController extends BaseController
{
    protected $salesOrderModel;
    private LandingOrderTable $landingOrderModel;

    public function __construct()
    {
        $this->salesOrderModel = new SalesOrderModel();
        $this->customersModel = new CustomersModel();
        $this->salesOrderItemsModel = new SalesOrderItemsModel();
        $this->itemsModel = new ItemsModel();
        $this->landingOrderModel = new LandingOrderTable();
        $this->company_code = "001";
        $this->view = "WooCommerceOrderSync";
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
        
        // if (!hash_equals($webhookSignature, $computedSignature)) {
        //     return $this->response->setJSON(['message' => 'Invalid signature']);
        // }

        // Log the webhook data
        $this->logWebhookData($jsonData);
        $confirmFirst = getenv('CONFIRM_FIRST');
        
        if ($confirmFirst) {
            // Save JSON data to the landing table
            $this->landingOrderModel->insert([
                'json_data' => json_encode($jsonData),
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return $this->response->setJSON(['message' => 'Order received and pending approval']);
        } else {
            // Proceed with the existing process
            return $this->processOrder($jsonData);
        }
    }
    private function processOrder($jsonData)
    {
        $data = $this->formatOrder($jsonData);
        $result = $this->salesOrderModel->insert($data);
        if ($result) {
            $this->insertSalesOrderItems($jsonData, $data['ctranno']);
            return $this->response->setJSON(['message' => 'Order received']);
        } else {
            $error = $result;
            return $this->response->setJSON(['message' => $error]);
        }
    }

    private function formatOrder($jsonData){
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
        return $data;
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
                // Insert the new product into the database
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
            }
            $data = $this->formatSalesOrderItems($salesOrderId, $item, $product);
            $this->salesOrderItemsModel->insert($data);
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

    private function formatSalesOrderItems($salesOrderId, $item, $product){
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
        return $data;
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

    public function index()
    {
        $data = [
            'title' => 'WooCommerce Order Sync',
        ];
        return view($this->view . '/index', $data);
    }

    public function getPendingOrders()
    {
        $pendingOrders = $this->landingOrderModel->where('status', 'pending')->findAll();
        return $this->response->setJSON($pendingOrders);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Order',
            'id' => $id,
        ];
                // TODO these formatting below will be done when a landing order is viewed and it will show on a view page
        // TODO format orderData
        // TODO format orderItemsData
        // TODO format customerData if ever creating a new customer
        // TODO format itemData if ever creating a new item
        return view($this->view . '/edit', $data);
    }

    public function loadOrder($id)
    {
        $order = $this->landingOrderModel->find($id);
        if ($order) {
            $jsonData = json_decode($order['json_data'], true);
            $orderData = $this->formatOrder($jsonData);
            $data = [
                'orderData' => $orderData,
                'orderItemsData' => '',
            ];
            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON(['message' => 'Order not found'], 404);
        }
    }


    public function approveOrder($id)
    {
        $order = $this->landingOrderModel->find($id);
        if ($order) {
            $jsonData = json_decode($order['json_data'], true);
            $this->processOrder($jsonData);
            $this->landingOrderModel->update($id, ['status' => 'approved']);
            return $this->response->setJSON(['message' => 'Order approved and processed']);
        } else {
            return $this->response->setJSON(['message' => 'Order not found'], 404);
        }
    }

    public function rejectOrder($id)
    {
        $order = $this->landingOrderModel->find($id);
        if ($order) {
            $this->landingOrderModel->update($id, ['status' => 'rejected']);
            return $this->response->setJSON(['message' => 'Order rejected']);
        } else {
            return $this->response->setJSON(['message' => 'Order not found'], 404);
        }
    }
}