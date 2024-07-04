<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PinModel;
use App\Models\BIRFormsModel; 
use App\Models\BIRYearFormModel;
use App\Models\BIRYearModel;
class BIRYearFormController extends BaseController
{
    protected $formModel;
    protected $yearModel;
    protected $birYearFormModel;

    public function __construct()
    {
        $this->formModel = new BIRFormsModel();
        $this->yearModel = new BIRYearModel();
        $this->birYearFormModel = new BIRYearFormModel();

    }

    public function index()
    {
        if (!session()->get('pin_verified')) {
            return redirect()->to('/');
        }
        // $associations = $this->birYearFormModel->getAssociations();
        // dd($associations);
        return view('manage_bir_forms/index');
    }

    public function associations()
    {
        $associations = $this->birYearFormModel->getAssociations();
        return $this->response->setJSON($associations);
    }

    public function edit($yearId){
        $year = $this->yearModel->find($yearId);
        $forms = $this->formModel->findAll();
        $associatedForms = $this->birYearFormModel->getFormsByYear($yearId);
        $data = [
            'year' => $year->year,
            'forms' => $forms,
            'associatedForms' => array_column($associatedForms, 'form_id')
        ];
        //dd($data);
    }
}