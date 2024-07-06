<?php

namespace App\Controllers\UsersLicense;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersLicense\UsersLicenseModel;

class UsersLicenseController extends BaseController
{
    protected $usersLicenseModel;
    public function __construct()
    {
        $this->usersLicenseModel = new UsersLicenseModel();
        $this->view = 'UsersLicense/';
    }
    public function index()
    {
        $data = [
            'usersLicense' => $this->usersLicenseModel->getLicensesWithCompany()
        ];

        return view($this->view . 'index', $data);
    }

    public function edit($id){
        $data = [
            'license' => $this->usersLicenseModel->getLicense($id)
        ];
        return view($this->view . 'edit', $data);
    }

    public function update($id){
        $license = $this->usersLicenseModel->find($id);
        $license->value = $license->encryptNumber($this->request->getPost('license_number'));
        $this->usersLicenseModel->save($license);
        return redirect()->to(site_url('users-license'));
    }
}
