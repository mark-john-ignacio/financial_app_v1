<?php

namespace App\Entities\Customers;

use CodeIgniter\Entity\Entity;

class CustomersEntity extends Entity
{
    protected $datamap = [
        "customer_code" => "cempid",
        "customer_name" => "cname",
        "tin" => "ctin",
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
