<?php

namespace App\Controllers\Suppliers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\Files\File;
use App\Models\Suppliers\SuppliersModel;
use App\Entities\Suppliers\SuppliersEntity;

class SuppliersController extends BaseController
{
    private SuppliersModel $suppliersModel;
    protected $view;
    protected $company_code;
    protected $user_id;
    private SuppliersEntity $suppliersEntity;

    public function __construct()
    {
        $this->suppliersModel = new SuppliersModel();
        $this->view = 'Suppliers/';
        $this->company_code = session()->get('current_company')->company_code;
        $this->user_id = session()->get('user_id');
        $this->db = \Config\Database::connect();
        $this->suppliersEntity = new SuppliersEntity();

    }

    public function index()
    {
        $data = [
            'title' => 'Suppliers'
        ];
        return view($this->view . 'index', $data);
    }

    public function load()
    {
        $items = $this->suppliersModel->findAll();
        return $this->response->setJSON($items);
    }

    public function upload_form()
    {
        $data = [
            'errors' => [],
            'title' => 'Upload Suppliers'
        ];
        return view($this->view . 'upload_form', $data);
    }

    public function downloadTemplate()
    {
        $filePath = WRITEPATH . 'templates/suppliers-template.xlsx'; // Path to your file in the writable directory
        return $this->response->download($filePath, null);
    }

    public function upload()
    {
        $data['script'] = ['datatable'];
        $data['form_title'] = 'Import Suppliers';
        helper(['form', 'url']);
    
        if ($this->request->getMethod() == 'POST') {
            
            $filesize = null;
            if (!$filesize) {
                $filesize = ["value" => 1000000];
                $filesize = (object) $filesize;
            }
            $filesizeValue = $filesize->value;
    
            $input = $this->validate([
                'userfile' => [
                    'label' => 'Excel File',
                    'rules' => [
                        'uploaded[userfile]',
                        'ext_in[userfile,xlsx,xls]',
                        'mime_in[userfile,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel]',
                        'max_size[userfile,2048]', // Adjust the size limit as needed
                    ],
                ],
            ]);
    
            if (!$input) {
                $data['validation'] = $this->validator;
                $this->swal('error', 'Invalid File');
                return redirect()->to(url_to("suppliers-upload-form"));
            } else {
                $file = $this->request->getFile('userfile');
                $spreadsheet = IOFactory::load($file->getTempName());

                $template = WRITEPATH . 'templates/suppliers-template.xlsx';
                $templateSpreadsheet = IOFactory::load($template);

                // Compare the structure
                $isValid = $this->compareExcelStructure($templateSpreadsheet, $spreadsheet);

                if (!$isValid) {
                    $this->swal('error', 'Invalid File! Please download the template');
                    return redirect()->to(url_to("suppliers-upload-form"));
                }
    
                $sheetNames = $spreadsheet->getSheetNames();
                
                $sheet1Data = $spreadsheet->getSheet(0)->toArray();
                $sheet2Data = null;
                $sheet3Data = null;
              
                if (count($sheet1Data) <= 1) {
                    $this->swal('error', 'Empty File');
                    return redirect()->to(url_to("suppliers-upload-form"));
                } else{
                    if(count($sheetNames) == 3){
                        $sheet2Data = $spreadsheet->getSheet(1)->toArray();
                        $sheet3Data = $spreadsheet->getSheet(2)->toArray();
                    } else{
                        $this->swal('error', 'Invalid File! Please Download the Template');
                        return redirect()->to(url_to("suppliers-upload-form"));
                    }
                }
                $newName = strtotime(time()) . $file->getRandomName();
                @$isload = $file->move( WRITEPATH . 'uploads/items/', $newName);

                if ($sheet1Data) {
                    $validatedData = [];
                    $isValid = true;
                    $cellNumber = 1;
    
                    $supcode = null;
                    $regname = null;
                    $tradename = null;
                    $tinno = null;
                    $zip = null;

                    $class = null;
                    $type = null;
                    $terms = null;
                    $ewtcode = null;

                    $liabcode = null;
                    $currency = null;

                    $SuppCodesSheet1 = [];
    
                    foreach ($sheet1Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            $headers = array_map('strval', $row);
                            foreach ($headers as $header) {
                                if (stripos($header, 'Supplier Code') !== false)            {  $supcode = $header;     }
                                if (stripos($header, 'Registered Name') !== false)          {  $regname = $header;     }
                                if (stripos($header, 'Business / Trade Name') !== false)    {  $tradename = $header;   }
                                if (stripos($header, 'Tin No') !== false)                   {  $tinno = $header;       }
                                if (stripos($header, 'Zip Code') !== false)                 {  $zip = $header;         }
                                if (stripos($header, 'Types') !== false && stripos($header, '(From Supplier Types)') !== false)  {  $type = $header;         }
                                if (stripos($header, 'Classification') !== false)           {  $class = $header;       }
                                if (stripos($header, 'Terms') !== false)                    {  $terms = $header;       }
                                if (stripos($header, 'EWT Code') !== false)                 {  $ewtcode = $header;     }
                                if (stripos($header, 'Liability Code') !== false)           {  $liabcode = $header;    }
                                if (stripos($header, 'Default Currency') !== false)         {  $currency = $header;    }
                            }
                            continue;
                        }
    
                        $rowErrors = [];
                        $rowData = array_combine($headers, array_map('trim', $row));

                        if (!empty($rowData[$supcode])){
                            $SuppCodesSheet1[] = $rowData[$supcode];
                        } else{
                            $this->swal('error', 'Invalid File! Please Download the Template');
                            return redirect()->to(url_to("suppliers-upload-form"));
                        }

                        $cellNumber++;
                        $rowData['Cell Number'] = $cellNumber;
                        $SupplierModel = $this->suppliersModel;
                     
                        // Validation
                        if (empty($rowData[$supcode]) || empty($rowData[$regname]) || empty($rowData[$tradename]) || empty($rowData[$tinno])  || empty($rowData[$liabcode])) {
                            $rowErrors[] = '* Required fields must be filled';
                        }

                        $duplicateSupplier = $SupplierModel->where('ccode', $rowData[$supcode])->where('deleted', 0)->where('compcode', $this->company_code)->first();
                        if ($duplicateSupplier) {
                            $rowErrors[] = '* Supplier Code must be unique';
                        }
                        if (!empty($rowData[$zip])){
                            if (!is_numeric($rowData[$zip])) {
                                $rowErrors[] = '* Zip Code must be numeric';
                            }
                        }
                        if (!empty($rowData[$type])) {
                            $types = $this->db->table('supplier_type')->where('ccode', $rowData[$type])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($types)) {
                                $rowErrors[] = '* Type must exist';
                            }
                        }
                        if (!empty($rowData[$class])) {
                            try {
                                // Attempt to query the supplier_classification table
                                $classs = $this->db->table('supplier_classification')
                                    ->where('ccode', $rowData[$class])
                                    ->where('deleted', 0)
                                    ->where('compcode', $this->company_code)
                                    ->get()
                                    ->getRow();
                            } catch (\Exception $e) {
                                // If an exception occurs (e.g., table does not exist), query the groupings table instead
                                $classs = $this->db->table('groupings')
                                    ->where('ccode', $rowData[$class])
                                    ->where('ctype', 'SUPCLS')
                                    ->where('compcode', $this->company_code)
                                    ->get()
                                    ->getRow();
                            }
                            if (empty($classs)) {
                                // If no record is found in the groupings table, insert a new entry
                                $this->db->table('groupings')->insert([
                                    'ccode' => $rowData[$class],
                                    'cdesc' => $rowData[$class], // Assuming cdesc should be the same as ccode
                                    'ctype' => 'SUPCLS',
                                    'compcode' => $this->company_code,
                                    'deleted' => 0
                                ]);
                        
                                // Query the newly inserted entry
                                $classs = $this->db->table('groupings')
                                    ->where('ccode', $rowData[$class])
                                    ->where('ctype', 'SUPCLS')
                                    ->where('compcode', $this->company_code)
                                    ->get()
                                    ->getRow();
                            }
                            if (empty($classs)) {
                                $rowErrors[] = '* Classification must exist';
                            }
                        }
                        if (!empty($rowData[$terms])) {
                            $termss = $this->db->table('groupings')->where('ccode', $rowData[$terms])->where('deleted', 0)->where('ctype', 'TERMS')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($termss)) {
                                $rowErrors[] = '* Terms must exist';
                            }
                        }
                        if (!empty($rowData[$ewtcode])) {
                            $ewtcodes = $this->db->table('ewt_codes')->where('ctaxcode', $rowData[$ewtcode])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($ewtcodes)) {
                                $rowErrors[] = '* EWT Code must exist';
                            }
                        }
                        if (!empty($rowData[$liabcode])) {
                            $liabcodes = $this->db->table('accounts')->where('cacctno', $rowData[$liabcode])->where('ccategory', 'LIABILITIES')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($liabcodes)) {
                                $rowErrors[] = '* Liability Code must exist';
                            }
                        }
                        if (!empty($rowData[$currency])) {
                            $currencys = $this->db->table('currency_rate')->where('symbol', $rowData[$currency])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($currencys)) {
                                $rowErrors[] = '* Default Currency must exist';
                            }
                        }
                       
                        // Check for duplicate suppliers codes within the current import
                        foreach ($validatedData as $validatedIndex => $validatedRow) {
                            if ($validatedRow[$supcode] === $rowData[$supcode]) {
                                $rowErrors[] = '* Duplicate supplier code within the import file at cell number ' . ($validatedRow['Cell Number']); // Adding 1 to cell number to match Excel numbering
                                break; 
                            }
                        }
    
                        $rowData['errors'] = $rowErrors;
                        $validatedData[] = $rowData;
    
                        if (!empty($rowErrors)) {
                            $isValid = false;
                        }
                    }
    
                    $data['data'] = $validatedData;
                    $data['isValid'] = $isValid;
                    $data['sheetName1'] = $sheetNames[0];
                    $data['loaded_page'] = 'Suppliers/PreviewSuppliers';
                }

                if ($sheet2Data){
                    $validatedData2 = [];
                    $isValid2 = true;
                    $cellNumber2 = 1;
                    
                    $supcode2 = null;
                    $houseno = null;
                    $city = null;
                    $state = null;
                    $country = null;
                    $zipcode = null;

                    foreach ($sheet2Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header) {
                                if (stripos($header, 'Supplier Code') !== false) {
                                    $supcode2 = $header;
                                }
                                if (stripos($header, 'House No. / Building No. / Street') !== false) {
                                    $houseno = $header;
                                }
                                if (stripos($header, 'City') !== false) {
                                    $city = $header;
                                }
                                if (stripos($header, 'State') !== false) {
                                    $state = $header;
                                }
                                if (stripos($header, 'Country') !== false) {
                                    $country = $header;
                                }
                                if (stripos($header, 'Zip Code') !== false) {
                                    $zipcode = $header;
                                }
                            }
                            continue;
                        }

                        $rowErrors2 = [];
                        $rowData = array_combine($headers, array_map('trim', $row)); 
                        $cellNumber2++; 
                        $rowData['Cell Number'] = $cellNumber2;

                        // Validation
                        if (empty($rowData[$supcode2]) || empty($rowData[$houseno]) || empty($rowData[$city]) || empty($rowData[$state]) || empty($rowData[$country])) {
                            $rowErrors2[] = '* Required fields must be filled';
                        }

                        if (!empty($rowData[$zipcode])){
                            if (!is_numeric($rowData[$zipcode])) {
                                $rowErrors2[] = '* Zip Code must be numeric';
                            }
                        }
                        
                        $supcode = $this->db->table('suppliers')->where('ccode', $rowData[$supcode2])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                        
                        if (!in_array($rowData[$supcode2], $SuppCodesSheet1) && empty($supcode)) {
                            $rowErrors2[] = '* Supplier Code does not exist in the uploaded file and in Suppliers Table';
                        }

                        $rowData['errors2'] = $rowErrors2;
                        $validatedData2[] = $rowData;

                        if (!empty($rowErrors2)) {
                            $isValid2 = false;
                        }
                    }
                    
                    $data['data2'] = $validatedData2;
                    $data['isValid2'] = $isValid2;
                    $data['sheetName2'] = $sheetNames[1];
                    $data['loaded_page'] = 'Suppliers/PreviewSuppliers';
                }

                if ($sheet3Data){
                    $validatedData3 = [];
                    $isValid3 = true;
                    $cellNumber3 = 1;
                    
                    $supcode3 = null;
                    $contname3 = null;
                    $desig = null;
                    $dept = null;
                    $email3 = null;
                    $mobile3 = null;

                    foreach ($sheet3Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header) {
                                if (stripos($header, 'Supplier Code') !== false) {
                                    $supcode3 = $header;
                                }
                                if (stripos($header, 'Supplier Contact Name') !== false) {
                                    $contname3 = $header;
                                }
                                if (stripos($header, 'Designation') !== false) {
                                    $desig = $header;
                                }
                                if (stripos($header, 'Department') !== false) {
                                    $dept = $header;
                                }
                                if (stripos($header, 'Email Address') !== false) {
                                    $email3 = $header;
                                }
                                if (stripos($header, 'Mobile Number') !== false) {
                                    $mobile3 = $header;
                                }
                            }
                            continue;
                        }

                        $rowErrors3 = [];
                        $rowData = array_combine($headers, array_map('trim', $row)); 
                        $cellNumber3++;
                        $rowData['Cell Number'] = $cellNumber3;

                        // Validation
                        if (empty($rowData[$supcode3]) || empty($rowData[$contname3]) || empty($rowData[$desig]) || empty($rowData[$dept]) || empty($rowData[$email3]) || empty($rowData[$mobile3])) {
                            $rowErrors3[] = '* Required fields must be filled';
                        }
                        
                        $supplierercode3 = $this->db->table('suppliers')->where('ccode', $rowData[$supcode3])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                        
                        if (!in_array($rowData[$supcode3], $SuppCodesSheet1) && empty($supplierercode3)) {
                            $rowErrors3[] = '* Supplier code does not exist in the uploaded file and in Suppliers Table';
                        }
                        if (!empty($rowData[$email3])){
                            if (!filter_var($rowData[$email3], FILTER_VALIDATE_EMAIL)) {
                                $rowErrors3[] = '* Invalid email address';
                            }
                        }
                        if (!empty($rowData[$mobile3])){
                            if (!is_numeric($rowData[$mobile3])) {
                                $rowErrors3[] = '* Mobile Number must be numeric';
                            }
                        }

                        $rowData['errors3'] = $rowErrors3;
                        $validatedData3[] = $rowData;

                        if (!empty($rowErrors3)) {
                            $isValid3 = false;
                        }
                    }
                    
                    $data['data3'] = $validatedData3;
                    $data['isValid3'] = $isValid3;
                    $data['sheetName3'] = $sheetNames[2];
                    $data['loaded_page'] = 'Suppliers/PreviewSuppliers';
                }    
            }
            return view($this->view . 'upload_preview', $data);
        }
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

    public function insertSuppliers()
    {
        $data = json_decode($this->request->getPost('tableData'))->table1;
        //$data = $this->request->getPost('data');
        $data2 = $this->request->getPost('data2');
        $data3 = $this->request->getPost('data3');

        $success = false;
        $success2 = true; 
        $success3 = true; 
        $success3 = true; 

        if ($data) {
            $success = $this->inserttblDataSuppliers($data);
        }
        if ($data2) {
            $success2 = $this->inserttblAddressSup($data2);
        }
        if ($data3) {
            $success3 = $this->insertContactListSup($data3);
        }

        if ($success && $success2 && $success3) {
            $this->swal('success', 'Successfully Inserted');
            return redirect()->to(site_url('suppliers'));
        } else {
            $this->swal('error', 'Insertion Failed');
            return redirect()->to(site_url('suppliers'));
        }
    }


    private function inserttblDataSuppliers($data)
    {  
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'ccode' => 'Supplier Code',
            'cname' => 'Registered Name',
            'ctradename' => 'Business / Trade Name',
            'ctin' => 'Tin No',

            'chouseno' => 'House No. / Building No. / Street',
            'ccity' => 'City',
            'cstate' => 'State',
            'ccountry' => 'Country',
            'czip' => 'Zip Code',

            'csuppliertype' => 'Supplier Types',
            'csupplierclass' => 'Classification',
            'cterms' => 'Terms',
            'newtcode' => 'EWT Code',
            'cacctcode' => 'Liability Code',
            'cdefaultcurrency' => 'Default Currency',
        ];
        
        $success = true;
    
        foreach ($data as $row) {
            $rowData = [];
            
            // Loop through the field mappings
            foreach ($fieldMappings as $dbField => $keyword) {
                foreach ($row as $key => $value) {
                    
                    if (stripos($key, $keyword) !== false) {
                        if (in_array($dbField, ['cname', 'ctradename', 'ccity', 'cstate', 'ccountry'])) {
                            $rowData[$dbField] = strtoupper($value);
                        } else {
                            $rowData[$dbField] = $value;
                        }
                    }
                }
            }
            $currentDate = date('Y-m-d H:i:s');
            $rowData['compcode'] = $this->company_code;
            $rowData['created_by'] = $this->user_id;
            $rowData['created_date'] = $currentDate; 

            $logfile = array(
                'user_id' => $this->user_id,
                'created_by' => $this->user_id,
                'created_date' => $currentDate,
                'main_module' => 'Administrator',
                'sub_module' => 'Suppliers > Masterlist',
                'event' => 'MASS UPLOAD MASTERLIST',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
           
            if (!empty($rowData['ccode'])) {
                //$this->db->table('logfile_supplier_masterfile')->insert($logfile); 
                $supplier = new SuppliersEntity($rowData);
                $saveSuccess = $this->suppliersModel->insert($supplier);
                
                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }
    
        return $success;
    }

    private function inserttblAddressSup($data)
    {   
        $fieldMappings = [
            'ccode' => 'Supplier Code',
            'chouseno' => 'House No. / Building No. / Street',
            'ccity' => 'City',
            'cstate' => 'State',
            'ccountry' => 'Country',
            'czip' => 'Zip Code',
        ];
    
        $success = true;
    
        foreach ($data as $row) {
            $rowData = [];
    
            // Loop through the field mappings
            foreach ($fieldMappings as $dbField => $keyword) {
                foreach ($row as $key => $value) {
                    if (stripos($key, $keyword) !== false) {
                        if (in_array($dbField, ['ccity', 'cstate', 'ccountry'])) {
                            $rowData[$dbField] = strtoupper($value);
                        } else {
                            $rowData[$dbField] = $value;
                        }
                        break;
                    }
                }
            }

            $rowData['compcode'] = $this->company_code;

            $logfile = array(
                'user_id' => $this->user_id,
                'created_by' => $this->user_id,
                'created_date' => datetimedb,
                'main_module' => 'Administrator',
                'sub_module' => 'Suppliers > Masterfile',
                'event' => 'MASS UPLOAD ADDRESS',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['ccode'])) {
                $this->db->table('logfile_supplier_masterfile')->insert($logfile); 
                $saveSuccess = $this->db->table('suppliers_address')->insert($rowData);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }

        return $success;
    }

    
    private function insertContactListSup($data)
    {   
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'ccode' => 'Supplier Code',
            'cname' => 'Supplier Contact Name',
            'cdesignation' => 'Designation',
            'cdept' => 'Department',
            'cemail' => 'Email Address',
            'cmobile' => 'Mobile Number',
            'cphone' => 'Landline Number',
        ];

        $success = true;

        foreach ($data as $row) {
            $rowData = [];

            // Loop through the field mappings
            foreach ($fieldMappings as $dbField => $keyword) {
                // Find the column in the row that matches the keyword
                foreach ($row as $key => $value) {
                    if (stripos($key, $keyword) !== false) {
                        $rowData[$dbField] = $value;
                        break;
                    }
                }
            }

            $rowData['compcode'] = $this->company_code;

            $logfile = array(
                'user_id' => $this->user_id,
                'created_by' => $this->user_id,
                'created_date' => datetimedb,
                'main_module' => 'Administrator',
                'sub_module' => 'Suppliers > Masterfile',
                'event' => 'MASS UPLOAD CONTACT LIST',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['ccode'])) {
                $this->db->table('logfile_supplier_masterfile')->insert($logfile); 
                $saveSuccess = $this->db->table('suppliers_contacts')->insert($rowData);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }

        return $success;
    }
    public function deleteAll()
    {
        $this->suppliersModel->truncate();
        return $this->response->setJSON(['success' => true]);
    }
}
