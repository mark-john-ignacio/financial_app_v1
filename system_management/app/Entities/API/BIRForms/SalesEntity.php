<?php

namespace App\Entities\API\BIRForms;

use CodeIgniter\Entity;

class SalesEntity extends Entity
{
    protected $datamap = [
        'company' => 'compcode',
        'date' => 'ddate',
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}