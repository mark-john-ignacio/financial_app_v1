<?php
namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use App\Models\BIRForms\BIRFormModel;
use App\Models\BIRForms\BIRYearFormModel;
use App\Models\BIRForms\BIRYearModel;

class BIRYearFormController extends BaseController
{
    protected $formModel;
    protected $yearModel;
    protected $birYearFormModel;

    public function __construct()
    {
        $this->formModel = new BIRFormModel();
        $this->yearModel = new BIRYearModel();
        $this->birYearFormModel = new BIRYearFormModel();
        $this->view = 'BIRForms/YearForm/';

    }

    public function index()
    {
        if (!session()->get('pin_verified')) {
            return redirect()->to('/');
        }
        $data['availableYears'] = $this->yearModel->getYearsWithoutEntries();
        // $associations = $this->birYearFormModel->getAssociations();
        // dd($associations);
        return view($this->view.'index', $data);
    }

    public function associations()
    {
        $associations = $this->birYearFormModel->getAssociations();
        return $this->response->setJSON($associations);
    }

    public function new(){
        $yearId = $this->request->getPost("year_id");
        $year = $this->yearModel->find($yearId);
        $forms = $this->formModel->findAll();
        $associatedForms = $this->birYearFormModel->getFormsByYear($yearId);
        $data = [
            'year_id' => $yearId,
            'year' => $year->year,
            'forms' => $forms,
            'associatedForms' => array_column($associatedForms, 'form_id')
        ];
        return view($this->view.'edit', $data);

    }

    public function edit($yearId){
        $year = $this->yearModel->find($yearId);
        $forms = $this->formModel->findAll();
        $associatedForms = $this->birYearFormModel->getFormsByYear($yearId);
        $data = [
            'year_id' => $yearId,
            'year' => $year->year,
            'forms' => $forms,
            'associatedForms' => array_column($associatedForms, 'form_id')
        ];
        return view($this->view.'edit', $data);
    }

    public function update($yearId){
        $form_ids = $this->request->getPost('forms');
        $this->birYearFormModel->where(['year_id' => $yearId]);
        $this->birYearFormModel->delete();

        if(!empty($form_ids)){
            $data = [];
            foreach($form_ids as $form_id){
                $data[] = [
                    'year_id' => $yearId,
                    'form_id' => $form_id,
                    'compcode' => session()->get('current_company')->company_code
                ];
            }
            $this->birYearFormModel->insertBatch($data);
        }
        return redirect()->to('/bir-forms/year-form');

    }
}