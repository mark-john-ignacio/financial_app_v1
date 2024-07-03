<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PinModel;
use App\Models\NavMenuFormsModel; 
use App\Models\BIRFormYearModel;
class ManageBIRFormsController extends BaseController
{
    protected $pinModel;
    protected $formModel;
    protected $yearModel;

    public function __construct()
    {
        $this->pinModel = new PinModel();
        $this->formModel = new NavMenuFormsModel();
        $this->yearModel = new BIRFormYearModel();
    }

    public function index()
    {
        if (!session()->get('pin_verified')) {
            return redirect()->to('/');
        }
        $data['forms'] = $this->formModel->findAll();
    
        // Fetch years and remove duplicates
        $years = array_map(function($yearObj) {
            return $yearObj->year_id; // Adjust based on actual structure
        }, $this->yearModel->findAll());
        $uniqueYears = array_unique($years);
        sort($uniqueYears); 

        $data['years'] = $uniqueYears;
        $data['years_forms'] = $this->yearModel->findAll();
        // dd($data['years_forms']);
    
        return view('manage_bir_forms/index', $data);
    }

    public function show()
    {
        if (!session()->get('pin_verified')) {
            return redirect()->to('/');
        }
        $yearId = $this->request->getpost('year_id');
        $registeredForms = $this->yearModel->getRegisteredFormsForYear($yearId);
        $registeredFormIds = array_map(function($formObj) {
            return $formObj->form_id; // Adjust based on actual structure
        }, $registeredForms);

        $data = [
            'registered_forms' => $registeredFormIds,
            'forms' => $this->formModel->findAll(),
        ];

        return $this->response->setJSON($data);
    }
}