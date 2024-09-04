<?php

namespace App\Entities\BIRForms;

use CodeIgniter\Entity\Entity;

class CompanyEntity extends Entity
{
    protected $datamap = [
        "company_code" => 'compcode',
        "company_name" => 'compname',
        "reporting_period" => 'reporting_period_type',
        "fiscal_month" => 'fiscal_month_start_end',
        "signature_image" => 'bir_sig_sign',
    ];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [];

}