<?php

namespace App\Entities\Suppliers;

use CodeIgniter\Entity\Entity;

class SuppliersEntity extends Entity
{
    protected $datamap = [
        "supplier_code" => "ccode",
        "supplier_name" => "cname",
        "tin" => "ctin",
        "status" => "cstatus",
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
