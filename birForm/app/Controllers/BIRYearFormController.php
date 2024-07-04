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
        return view('bir-year-form/index');
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
            'year_id' => $yearId,
            'year' => $year->year,
            'forms' => $forms,
            'associatedForms' => array_column($associatedForms, 'form_id')
        ];
        return view('bir-year-form/edit', $data);
    }

    public function update($yearId){
        $form_ids = $this->request->getPost('forms');
        $this->birYearFormModel->delete(['year_id' => $yearId]);

        if(!empty($form_ids)){
            $data = [];
            foreach($form_ids as $form_id){
                $data[] = [
                    'year_id' => $yearId,
                    'form_id' => $form_id
                ];
            }
            $this->birYearFormModel->insertBatch($data);
        }

        return redirect()->to('/bir-year-form');

    }
}