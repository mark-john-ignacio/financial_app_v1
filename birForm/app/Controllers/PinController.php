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

    public function setPin(){
        $new_pin = $this->request->getPost('new_pin');
        $encrypted_pin = $this->encrypter->encrypt($new_pin);
        $this->pinModel->setPin($encrypted_pin);
        return redirect()->to('/');
        
    }
    public function verifyPin(){
        $pin = $this->request->getPost('pin');
        if ($this->pinModel->verifyPin($pin)) {
            return redirect()->to('/birFormsManagement/formYearAssociation');
        } else {
            return redirect()->back()->with('error', 'Incorrect Pin');
        }
    }
}
