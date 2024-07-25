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
    protected $view;

    public function __construct()
    {
        $this->customerModel = new CustomersModel();
        $this->view = 'Customers/';
    }

    public function index()
    {
        return view($this->view . 'index');
    }

    public function load()
    {
        $customers = $this->customerModel->findAll();
        return $this->response->setJSON($customers);
    }

    public function upload_form()
    {
        return view($this->view . 'upload_form', ['errors' => []]);
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

        $spreadsheet = $this->loadSpreadsheet($file->getTempName());
        $sheetData = $this->getSheetData($spreadsheet);

        if (!$sheetData) {
            $errors[] = 'The uploaded file is empty or does not contain the required data.';
            $data = ['errors' => $errors];
            return view($this->view . 'upload_form', $data);
        }

        // Move the uploaded file to the desired location
        $newName = strtotime(time()) . $file->getRandomName();
        $destinationPath = WRITEPATH . 'uploads/customers/';

        if (!$file->move($destinationPath, $newName)) {
            $errors[] = 'Failed to move the uploaded file.';
            $data = ['errors' => $errors];
            return view($this->view . 'upload_form', $data);
        }

        if ($sheetData) {
            $validationResult = $this->validateSheetData($sheetData);
            if ($validationResult !== true) {
                return $validationResult;
            }
        }

        // Further processing if needed
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

    private function getSheetData($spreadsheet)
    {
        $sheetNames = $spreadsheet->getSheetNames();
        $sheet1Data = $spreadsheet->getSheet(0)->toArray();

        if (count($sheet1Data) <= 1 || count($sheetNames) != 4) {
            return false;
        }

        return [
            'sheet1' => $sheet1Data,
            'sheet2' => $spreadsheet->getSheet(1)->toArray(),
            'sheet3' => $spreadsheet->getSheet(2)->toArray(),
            'sheet4' => $spreadsheet->getSheet(3)->toArray(),
        ];
    }

    private function validateSheetData($sheetData)
    {
        $headers = $this->getHeaders($sheetData['sheet1']);
        $requiredFields = ['Customer Code', 'Registered Name', 'Business / Trade Name', 'Tin No', 'AR Code'];

        foreach ($sheetData['sheet1'] as $index => $row) {
            if ($index == 0) continue;

            $rowData = array_combine($headers, array_map('trim', $row));
            $rowErrors = $this->validateRowData($rowData, $requiredFields);

            if (!empty($rowErrors)) {
                return $this->handleError(implode(', ', $rowErrors));
            }
        }

        return true;
    }

    private function getHeaders($sheet1Data)
    {
        $headers = array_map('strval', $sheet1Data[0]);
        return $headers;
    }

    private function validateRowData($rowData, $requiredFields)
    {
        $rowErrors = [];

        foreach ($requiredFields as $field) {
            if (empty($rowData[$field])) {
                $rowErrors[] = "* $field must be filled";
            }
        }

        if (!empty($rowData['Zip Code']) && !is_numeric($rowData['Zip Code'])) {
            $rowErrors[] = '* Zip Code must be numeric';
        }

        if (!empty($rowData['Credit Limit']) && !is_numeric($rowData['Credit Limit'])) {
            $rowErrors[] = '* Credit Limit must be numeric';
        }

        return $rowErrors;
    }

    private function handleError($message)
    {
        $this->swal('error', $message);
        return view($this->view . 'upload_form');
    }
}