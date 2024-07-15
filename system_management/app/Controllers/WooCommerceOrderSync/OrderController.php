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
        $jsonData = $this->request->getJSON(true);
        $data = [
            'compcode' => "001",
            'ctranno' => $jsonData['order_id'],
            'ccode' => $jsonData['customer_name'],
            'ddate' => $jsonData['order_date'],
            'ngross' => $jsonData['amount']
        ];

        if($this->salesOrderModel->insert($data)){
            return $this->response->setJSON(['message' => 'Order received']);
        }else{ 
            return $this->response->setJSON(['message' => 'Order failed']);
        }
    }
}
