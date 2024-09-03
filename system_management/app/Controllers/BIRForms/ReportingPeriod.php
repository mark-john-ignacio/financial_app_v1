<?php

namespace App\Controllers\BIRForms;

use App\Controllers\BaseController;
use App\Models\BIRForms\CompanyModel;
use CodeIgniter\API\ResponseTrait;
use App\Entities\BIRForms\CompanyEntity;
use CodeIgniter\Exceptions\PageNotFoundException;

class ReportingPeriod extends BaseController
{
    use ResponseTrait;
    private CompanyModel $companyModel;

    public function __construct() {
        $this->companyModel = new CompanyModel();
        $this->view = 'BIRForms/ReportingPeriod/';
    }

    public function index(){
        $data = [
            'title' => 'Reporting Period',
        ];
        return view($this->view . 'index', $data);
    }

    public function load()
    {
        $companies = $this->companyModel->findAll();
        return $this->respond($companies);
    }

    public function edit($id)
    {
        $company = $this->getEntryOr404($id);
        $data = [
            'title' => 'Edit Reporting Period',
            'company' => $company,
        ];
        return view($this->view . 'edit', $data);
    }

    public function update($id)
    {
        $company = $this->getEntryOr404($id);
        $company->fill($this->request->getPost());

        $company->__unset('_method');
        
        if (!$company->hasChanged()){
            return redirect()->back()
            ->with('error', 'Nothing to update');
        }
        
        if (!$this->companyModel->save($company)){
            return redirect()->back()->with('errors', $this->companyModel->errors());
        }

        return redirect()->to(site_url('bir-forms/reporting-period'))->with('message', 'Reporting Period updated');
    }

    private function getEntryOr404($id): CompanyEntity
    {
        $entry = $this->companyModel->find($id);
        if ($entry === null){
            throw new PageNotFoundException("Form with id:$id not found");
        }

        return $entry;
    }

}