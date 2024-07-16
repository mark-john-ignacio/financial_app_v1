<?php

namespace App\Controllers\WooCommerceOrderSync;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WooCommerceOrderSync\SalesOrderModel;
use CodeIgniter\RESTful\ResourceController;

class OrderController extends BaseController
{
    protected $salesOrderModel;

    public function __construct()
    {
        $this->salesOrderModel = new SalesOrderModel();
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
        
        // Insert the order data into the database
        $data = [
            'compcode' => "001",
            'ctranno' => $jsonData['id'],
            'ccode' => $jsonData['billing']['first_name'] . ' ' . $jsonData['billing']['last_name'],
            'ddate' => $jsonData['date_created'],
            'ngross' => $jsonData['total']
        ];

        if($this->salesOrderModel->insert($data)){
            return $this->response->setJSON(['message' => 'Order received']);
        }else{ 
            return $this->response->setJSON(['message' => 'Order failed']);
        }
    }
}
