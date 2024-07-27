<?php

namespace App\Entities\WooCommerceOrderSync;

use CodeIgniter\Entity\Entity;

class SalesOrderEntity extends Entity
{
    protected $datamap = [
        "company_code" => "compcode",
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
