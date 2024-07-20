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
        $customerCode = $this->getCustomerCode($jsonData);
        $data = [
            'compcode' => $this->company_code,
            'ctranno' => $this->salesOrderModel->generateSONumber($this->company_code),
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

        if($this->salesOrderModel->insert($data)){
            return $this->response->setJSON(['message' => 'Order received']);
        }else{ 
            return $this->response->setJSON(['message' => 'Order failed']);
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
