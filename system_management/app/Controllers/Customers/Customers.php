<?php

namespace App\Controllers\Customers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use CodeIgniter\Files\File;

use App\Models\Customers\CustomersModel;

class Customers extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomersModel();
        $this->view = 'Customers/';
    }

    public function index()
    {
        return view($this->view.'index');
    }

    public function load()
    {
        $customers = $this->customerModel->findAll();
        return $this->response->setJSON($customers);
    }
    
    public function upload_form()
    {
        return view($this->view.'upload_form', ['errors' => []]);
    }

    public function upload()
    {
        $validationRule = $this->getValidationRules();

        if (!$this->validateData([], $validationRule)) {
            $data = ['errors' => $this->validator->getErrors()];
            $this->swal('error', 'Please check the form for errors.');
            return view($this->view . 'upload_form', $data);
        }
    
        $file = $this->request->getFile('userfile');
        $errors = $this->processUploadedFile($file);
    
        if (!empty($errors)) {
            $data = ['errors' => $errors];
            return view($this->view . 'upload_form', $data);
        }

        if (! $file->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $file->store();

            $data = ['uploaded_fileinfo' => new File($filepath)];

            return view($this->view.'upload_success', $data);
        }

        $data = ['errors' => 'The file has already been moved.'];

        return view($this->view.'upload_form', $data);
    }

    private function getValidationRules()
    {
        return [
            'userfile' => [
                'label' => 'Excel File',
                'rules' => [
                    'uploaded[userfile]',
                    'ext_in[userfile,xlsx,xls]',
                    'mime_in[userfile,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel]',
                    'max_size[userfile,2048]', // Adjust the size limit as needed
                ],
            ],
        ];
    }

    private function processUploadedFile($file)
    {
        try {
            $uploadedSpreadsheet = $this->loadSpreadsheet($file->getTempName());
            $templatePath = WRITEPATH . 'templates/customertemplate.xlsx';
            $templateSpreadsheet = $this->loadSpreadsheet($templatePath);

            if (!$this->compareExcelStructure($templateSpreadsheet, $uploadedSpreadsheet)) {
                return ['The uploaded file does not match the template.'];
            }

            // Proceed with further processing of the spreadsheet
            return [];
        } catch (\Exception $e) {
            return [$e->getMessage()];
        }
    }

    private function loadSpreadsheet($filePath)
    {
        return IOFactory::load($filePath);
    }

    private function compareExcelStructure($templateSpreadsheet, $uploadedSpreadsheet)
    {
        // Get sheet names
        $templateSheetNames = $templateSpreadsheet->getSheetNames();
        $uploadedSheetNames = $uploadedSpreadsheet->getSheetNames();

        // Check if the number of sheets is the same
        if (count($templateSheetNames) !== count($uploadedSheetNames)) {
            return false;
        }

        // Check if sheet names match
        foreach ($templateSheetNames as $index => $templateSheetName) {
            if ($templateSheetName !== $uploadedSheetNames[$index]) {
                return false;
            }

            // Compare the data of each sheet
            $templateSheet = $templateSpreadsheet->getSheet($index)->toArray();
            $uploadedSheet = $uploadedSpreadsheet->getSheet($index)->toArray();

            // Check if the number of columns is the same
            if (count($templateSheet[0]) !== count($uploadedSheet[0])) {
                return false;
            }

            // Check if column headers match
            for ($i = 0; $i < count($templateSheet[0]); $i++) {
                if ($templateSheet[0][$i] !== $uploadedSheet[0][$i]) {
                    return false;
                }
            }
        }

        return true;
    }
}
