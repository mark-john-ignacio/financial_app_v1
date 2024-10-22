<?php

namespace App\Controllers\Suppliers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Suppliers\SuppliersModel;
use App\Entities\Suppliers\SuppliersEntity;
use App\Services\ExcelService;
use App\Services\SupplierImportService;
use App\Services\ValidationService;

class SuppliersController extends BaseController
{
    private SuppliersModel $suppliersModel;
    private ExcelService $excelService;
    private SupplierImportService $supplierImportService;
    private ValidationService $validationService;
    protected $view;
    protected $company_code;
    protected $user_id;

    public function __construct()
    {
        $this->suppliersModel = new SuppliersModel();
        $this->excelService = new ExcelService();
        $this->supplierImportService = new SupplierImportService();
        $this->validationService = new ValidationService();
        $this->view = 'Suppliers/';
        $this->company_code = session()->get('current_company')->company_code;
        $this->user_id = session()->get('user_id');
    }

    public function index()
    {
        return view($this->view . 'index', ['title' => 'Suppliers']);
    }

    public function load()
    {
        $items = $this->suppliersModel->findAll();
        return $this->response->setJSON($items);
    }

    public function upload_form()
    {
        return view($this->view . 'upload_form', ['errors' => [], 'title' => 'Upload Suppliers']);
    }

    public function downloadTemplate()
    {
        $filePath = WRITEPATH . 'templates/suppliers-template.xlsx';
        return $this->response->download($filePath, null);
    }

    public function upload()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to(url_to("suppliers-upload-form"));
        }

        $validation = $this->validationService->validateUploadedFile();
        if (!$validation) {
            return $this->handleValidationError();
        }

        $file = $this->request->getFile('userfile');
        $spreadsheet = $this->excelService->loadSpreadsheet($file);

        if (!$this->excelService->isValidTemplate($spreadsheet)) {
            return $this->handleInvalidTemplate();
        }

        $data = $this->supplierImportService->processSpreadsheet($spreadsheet);
        return view($this->view . 'upload_preview', $data);
    }

    public function insertSuppliers()
    {
        $data = json_decode($this->request->getPost('tableData'))->table1;
        $data2 = $this->request->getPost('data2');
        $data3 = $this->request->getPost('data3');

        $success = $this->supplierImportService->insertSuppliers($data, $data2, $data3);

        if ($success) {
            $this->swal('success', 'Successfully Inserted');
        } else {
            $this->swal('error', 'Insertion Failed');
        }

        return redirect()->to(site_url('suppliers'));
    }

    public function deleteAll()
    {
        $this->suppliersModel->truncate();
        return $this->response->setJSON(['success' => true]);
    }

    private function handleValidationError()
    {
        $this->swal('error', 'Invalid File');
        return redirect()->to(url_to("suppliers-upload-form"));
    }

    private function handleInvalidTemplate()
    {
        $this->swal('error', 'Invalid File! Please download the template');
        return redirect()->to(url_to("suppliers-upload-form"));
    }
}