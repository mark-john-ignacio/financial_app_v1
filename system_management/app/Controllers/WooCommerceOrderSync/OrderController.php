<?php

namespace App\Controllers\WooCommerceOrderSync;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WooCommerceOrderSync\SalesOrderModel;
use App\Models\WooCommerceOrderSync\CustomersModel;
use CodeIgniter\RESTful\ResourceController;

class OrderController extends BaseController
{
    protected $salesOrderModel;

    public function __construct()
    {
        $this->salesOrderModel = new SalesOrderModel();
        $this->customersModel = new CustomersModel();
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
        // if (!hash_equals($webhookSignature, $computedSignature)) {
        //     return $this->response->setJSON(['message' => 'Invalid signature']);
        // }
        
        // Insert the order data into the database
        $data = [
            'compcode' => $this->company_code,
            'ctranno' => $this->generateSONumber(),
            'ccode' => $this->getCustomerCode($jsonData['billing']['first_name'] . ' ' . $jsonData['billing']['last_name']),
            'ddate' => $jsonData['date_created'],
            'ngross' => $jsonData['total'],
            'dcutdate' => $jsonData['date_created'],
            'dpodate' => $jsonData['date_created'],
            'cpono' => $jsonData['id'],
        ];

        if($this->salesOrderModel->insert($data)){
            return $this->response->setJSON(['message' => 'Order received']);
        }else{ 
            return $this->response->setJSON(['message' => 'Order failed']);
        }
    }

    private function generateSONumber() {
        $current_month = date('m');
        $current_day = date('d');
        $current_year = date('y');
        $batch_number = "SO" . $current_month . $current_day . $current_year;
        $batchNumbersCount = $this->salesOrderModel->where('compcode', $this->company_code)->like('ctranno', $batch_number, 'after')->countAllResults();
    
        if ($batchNumbersCount == 0){
            return $batch_number . "_1";
        } else {
            $batchNumbers = $this->salesOrderModel->where('compcode', $this->company_code)->like('ctranno', $batch_number, 'after')->findAll();
            $highestCounter = 0;
            foreach ($batchNumbers as $batchNumber) {
                $counter = (int)explode('_', $batchNumber->ctranno)[1];
                if ($counter > $highestCounter) {
                    $highestCounter = $counter;
                }
            }
            return $batch_number . "_" . ($highestCounter + 1);
        }
    }

    private function getCustomerCode($customer_name){
        $customerName = strtoupper($customer_name);
        $customer = $this->customersModel->where('compcode', $this->company_code)->where('cname', $customerName)->first();
        if ($customer){
            return $customer->cempid;
        }else{
            $data = [
                'compcode' => $this->company_code,
                'cempid' => $this->generateCustomerCode(),
                'cname' => $customerName,
            ];
            $this->customersModel->insert($data);
            return $data['cempid'];
        }
    }

    private function generateCustomerCode(){
        // Fetch the highest customer code from the database
        $highestCode = $this->customersModel
                            ->where('compcode', $this->company_code)
                            ->where('cempid LIKE', 'CUST%')
                            ->orderBy('cempid', 'desc')
                            ->first();
    
        if ($highestCode) {
            // Extract the numeric part of the highest code
            $numberPart = intval(substr($highestCode->cempid, 4));
        } else {
            // If no codes exist, start from 0
            $numberPart = 0;
        }
    
        // Increment the number to get the next unique number
        $newNumber = $numberPart + 1;
    
        // Format the new number to maintain leading zeros
        $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    
        // Generate the new customer code
        $newCustomerCode = "CUST" . $formattedNumber;
    
        return $newCustomerCode;
    }
}
