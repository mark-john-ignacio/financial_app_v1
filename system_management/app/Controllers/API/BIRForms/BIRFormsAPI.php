<?php

namespace App\Controllers\API\BIRForms;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BIRForms\BIRFormModel;
use App\Models\API\BIRFormsAPIModel;
use CodeIgniter\API\ResponseTrait;

class BirFormsApi extends ResourceController
{
    use ResponseTrait;
    
    protected $model;
    protected $apiModel;

    public function __construct()
    {
        $this->model = model(BIRFormsAPIModel::class);
        $this->birFormModel = model(BIRFormModel::class);
    }

    public function getCompanyInfo()
    {
        $companyId = $this->request->getVar('companyId');
        $data = $this->model->getCompanyInfo($companyId);
        return $this->respond($data);
    }

    public function getApvData()
    {
        $companyId = $this->request->getVar('companyId');
        $year = $this->request->getVar('year');
        $quarter = $this->request->getVar('quarter');
        $data = $this->model->getApvData($companyId, $year, $quarter);
        return $this->respond($data);
    }

    public function getDefaultAccounts()
    {
        $companyId = $this->request->getVar('companyId');
        $data = $this->model->getDefaultAccounts($companyId);
        return $this->respond($data);
    }
}