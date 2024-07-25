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
        $validationRule = [
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
        if (! $this->validateData([], $validationRule)) {
            $data = ['errors' => $this->validator->getErrors()];

            return view($this->view.'upload_form', $data);
        }

        $img = $this->request->getFile('userfile');

        if (! $img->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $img->store();

            $data = ['uploaded_fileinfo' => new File($filepath)];

            return view($this->view.'upload_success', $data);
        }

        $data = ['errors' => 'The file has already been moved.'];

        return view($this->view.'upload_form', $data);
    }
}
