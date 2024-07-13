<?php

namespace App\Controllers\UsersLicense;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersLicense\UsersLicenseModel;
use App\Models\UsersLicense\CompanyModel;

class UsersLicenseController extends BaseController
{
    protected $usersLicenseModel;
    protected $companyModel;
    public function __construct()
    {
        $this->usersLicenseModel = new UsersLicenseModel();
        $this->companyModel = new CompanyModel();
        $this->view = 'UsersLicense/';
    }
    public function index()
    {
        $licensesWithCompany = $this->usersLicenseModel->getLicensesWithCompany();
        foreach ($licensesWithCompany as $license) {
            $company = $this->companyModel->find($license->compcode);
            $license->setKey($company->code);

            $license->number = $license->getDecryptedNumber();
        }
        $data = [
            'usersLicense' => $licensesWithCompany
        ];
        // dd($data);

        return view($this->view . 'index', $data);
    }

    public function edit($id){
        $license = $this->usersLicenseModel->getLicense($id);
        $company = $this->companyModel->find($license->compcode);
        $license->setKey($company->code);
        $license->number = $license->getDecryptedNumber();
        $data = [
            'license' => $license
        ];
    
        return view($this->view . 'edit', $data);
    }

    public function update($id){
        $license = $this->usersLicenseModel->find($id);
        $company = $this->companyModel->find($license->compcode);

        $license->setKey($company->code);
        $license->value = $license->encryptNumber($this->request->getPost('license_number'));
        $this->usersLicenseModel->save($license);
        return redirect()->to(site_url('users-license'));
    }
}
