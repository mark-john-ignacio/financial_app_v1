<?php

namespace App\Controllers\API\BIRForms;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\API\BIRForms\SalesModel;

class BIRForm2550M extends ResourceController
{
    use ResponseTrait;
    protected SalesModel $salesModel;
    

    public function __construct()
    {
        $this->salesModel = model(SalesModel::class);
    }

    public function getSalesPerMonth()
    {
        $json = $this->request->getJSON();
        $company_code = $json->company_code;
        $year = $json->year;
        $month = $json->month;
        $totalSales = $this->salesModel->getSalesPerMonth($month, $year, $company_code);
        return $this->respond($totalSales);
    }

}