<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PinModel; 
use App\Models\CompanySwitcher\CompanyModel;

class PinController extends BaseController
{
    protected $pinModel;
    protected $companyModel;
    protected $encrypter;

    public function __construct()
    {
        $this->pinModel = new PinModel();
        $this->companyModel = new CompanyModel();
        $this->encrypter = service('encrypter');
    }

    public function changePin(){
        if (!session()->get('pin_verified')) {
            return redirect()->to(site_url("/"));
        }
        return view('change_pin');
    }

    public function setPin(){
        
        $new_pin = $this->request->getPost('new_pin');
        $old_pin = $this->request->getPost('old_pin');
        // Retrieve the hashed pin from the database
        $hashed_pin = $this->pinModel->getHashedPin(); 
        if (!password_verify($old_pin, $hashed_pin)) {
            return redirect()->back()->with('error', 'Incorrect Old Pin');
        }
        session()->remove('pin_verified');
        // Hash the pin
        $hashed_pin = password_hash($new_pin, PASSWORD_DEFAULT);
        $this->pinModel->setPin($hashed_pin);
        return redirect()->to(site_url("/"))->with('message', 'Pin has been changed');
    }
    public function verifyPin(){
        $pin = $this->request->getPost('pin');
        // Retrieve the hashed pin from the database
        $hashed_pin = $this->pinModel->getHashedPin(); // Assume this method retrieves the hashed pin
        if (password_verify($pin, $hashed_pin)) {
            session()->set('pin_verified', true); 
            $companies = $this->companyModel->findAll();
            session()->set('companies', $companies);
            $current_company = $this->companyModel->first();
            session()->set('current_company', $current_company);
            return redirect()->to(site_url("bir-forms/year-form"));
        } else {
            return redirect()->back()->with('error', 'Incorrect Pin');
        }
    }

    public function logout(){
        session()->remove('pin_verified');
        session()->remove('companies');
        return redirect()->to('/');
    }
}
