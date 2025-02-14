<?php

namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BIRForms\BIRFormModel;
use App\Models\BIRForms\BIRYearFormModel;
use App\Entities\BIRForms\FormEntity;
use CodeIgniter\Exceptions\PageNotFoundException;

class BIRFormController extends BaseController
{
    protected $formModel;
    private BIRYearFormModel $yearFormModel;
    
    public function __construct()
    {
        $this->formModel = new BIRFormModel();
        $this->yearFormModel = new BIRYearFormModel();
        $this->view = 'BIRForms/Form/';
    }

    public function index()
    {
        return view($this->view.'index');
    }

    public function load()
    {
        $forms = $this->formModel->findAll();
        return $this->response->setJSON($forms);
    }

    public function new()
    {
        return view($this->view.'new', ['form' => new FormEntity]);
    }

    public function create()
    {
        $form = new FormEntity($this->request->getPost());
        $id = $this->formModel->insert($form);
        if ($id===false){
            return redirect()->back()->with('errors', $this->formModel->errors());
        }

        return redirect()->to(site_url('bir-forms/form'));
    }

    public function edit($id)
    {
        $data = ['form' => $this->formModel->find($id)];
        return view($this->view.'edit', $data);
    }

    public function update($id)
    {
        $form = $this->getEntryOr404($id);

        $form->fill($this->request->getPost());

        $form->__unset('_method');
        
        if (!$form->hasChanged()){
            return redirect()->back()
            ->with('error', 'Nothing to update');
        }
        
        if (!$this->formModel->save($form)){
            return redirect()->back()->with('errors', $this->formModel->errors());
        }

        return redirect()->to(site_url('bir-forms/form/'.$id))->with('message', 'Form updated');
    }

    public function delete($id)
    {
        $formRegistration = $this->yearFormModel->where('form_id', $id)->findAll();
        if (!empty($formRegistration)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cannot delete the form because it is referenced in Year-Form.'
            ])->setStatusCode(400);
        }
    
        $this->formModel->delete($id);
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Form deleted successfully'
        ])->setStatusCode(200);
    }

    public function show($id)
    {
        $data = ['form' => $this->formModel->find($id)];
        return view($this->view.'show', $data);
    }

    private function getEntryOr404($id): FormEntity
    {
        $entry = $this->formModel->find($id);
        if ($entry === null){
            throw new PageNotFoundException("Form with $id id not found");
        }

        return $entry;
    }
}
