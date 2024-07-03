<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PinModel; // Assume this model handles pin verification and form data retrieval
use App\Models\FormModel; // Handles form-year associations and queries

class ManageBIRFormsController extends BaseController
{
    protected $pinModel;
    protected $formModel;

    public function __construct()
    {
        $this->pinModel = new PinModel();
        $this->formModel = new FormModel();
    }

    public function index()
    {
        if (!session()->get('pin_verified')) {
            return redirect()->to('/');
        }
        return view('manage_bir_forms/index');
    }
}