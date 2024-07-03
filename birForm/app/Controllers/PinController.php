<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PinModel; 

class PinController extends BaseController
{
    protected $pinModel;
    protected $encrypter;

    public function __construct()
    {
        $this->pinModel = new PinModel();
        $this->encrypter = service('encrypter');
    }

    public function changePin(){
        if (!session()->get('pin_verified')) {
            return redirect()->to('/');
        }
        return view('change_pin');
    }

    public function setPin(){
        session()->remove('pin_verified');
        $new_pin = $this->request->getPost('new_pin');
        // Hash the pin
        $hashed_pin = password_hash($new_pin, PASSWORD_DEFAULT);
        $this->pinModel->setPin($hashed_pin);
        return redirect()->to('/');
    }
    public function verifyPin(){
        $pin = $this->request->getPost('pin');
        // Retrieve the hashed pin from the database
        $hashed_pin = $this->pinModel->getHashedPin(); // Assume this method retrieves the hashed pin
        if (password_verify($pin, $hashed_pin)) {
            session()->set('pin_verified', true);            
            return redirect()->to('/manage-bir');
        } else {
            return redirect()->back()->with('error', 'Incorrect Pin');
        }
    }

    public function logout(){
        session()->remove('pin_verified');
        return redirect()->to('/');
    }
}
