<?php

namespace App\Controllers\CompanySwitcher;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CompanySwitcher\CompanyModel;

class CompanySwitcherController extends BaseController
{
    protected $companyModel;
    public function getAllCompanies()
    {
        $companies = $this->companyModel->findAll();
        $data = [
            'companies' => $companies,
        ];
        return view('CompanySwitcher/index', $data);
    }
}
