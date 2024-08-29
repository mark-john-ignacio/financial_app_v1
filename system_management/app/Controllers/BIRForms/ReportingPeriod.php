<?php

namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use App\Models\BIRForms\CompanyModel;

class ReportingPeriod extends BaseController
{
    protected CompanyModel $companyModel;

    public function _construct() {
        $this->companyModel = model(CompanyModel::class);
    }

}