<?php

namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BIRForms\BIRFormModel;
use App\Entities\BIRForms\FormEntity;
use CodeIgniter\Exceptions\PageNotFoundException;

class BIRFormController extends BaseController
{
    protected $formModel;
    
    public function __construct()
    {
        $this->formModel = new BIRFormModel();
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

        if (!form->hasChanged()){
            return redirect()->back()
            ->with('message', 'Nothing to update');
        }
        return redirect()->to(site_url('bir-forms/form'));
    }

    public function delete($id)
    {
        $this->formModel->delete($id);
        return redirect()->to(site_url('bir-forms/form'));
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
