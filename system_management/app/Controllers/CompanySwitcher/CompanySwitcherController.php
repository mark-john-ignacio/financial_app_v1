<?php

namespace App\Controllers\CompanySwitcher;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CompanySwitcher\CompanyModel;

class CompanySwitcherController extends BaseController
{
    protected $companyModel;
    public function __construct()
    {
        $this->companyModel = new CompanyModel();
    }

    public function switchCompany($company_code)
    {
        $company = $this->companyModel->where('compcode', $company_code)->first();
        session()->set('current_company', $company);
        return redirect()->back()->with('message', 'Company has been switched');
    }
}
