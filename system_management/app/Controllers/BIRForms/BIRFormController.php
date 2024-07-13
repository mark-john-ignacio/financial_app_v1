<?php

namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BIRForms\BIRFormModel;

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
        return view($this->view.'new');
    }

    public function create()
    {
        $data = $this->request->getPost();
        $this->formModel->save($data);
        return redirect()->to(site_url('bir-forms/form'));
    }

    public function edit($id)
    {
        $data = ['form' => $this->formModel->find($id)];
        return view($this->view.'edit', $data);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $this->formModel->update($id, $data);
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
        //return view($this->view.'show', $data);
    }
}
