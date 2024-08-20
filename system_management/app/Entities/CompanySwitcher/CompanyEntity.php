<?php

namespace App\Entities\CompanySwitcher;

use CodeIgniter\Entity\Entity;

class CompanyEntity extends Entity
{
    protected $datamap = [
        'company_code' => 'compcode',
        'company_name' => 'compname',
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];
}
