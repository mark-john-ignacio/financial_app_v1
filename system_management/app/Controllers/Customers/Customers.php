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
        $this->company_code = isset(session()->company_code) ? session()->company_code : '001';
        $this->user_id = isset(session()->user_id) ? session()->user_id : '1';
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
        $data['script'] = ['datatable'];
        $data['form_title'] = 'Import Customers';
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
                return redirect()->to(base_url() . '/customers/upload_form');
            } else {
                $file = $this->request->getFile('userfile');
                $spreadsheet = IOFactory::load($file->getTempName());

                $template = WRITEPATH . 'templates/customertemplate.xlsx';
                $templateSpreadsheet = IOFactory::load($template);

                // Compare the structure
                $isValid = $this->compareExcelStructure($templateSpreadsheet, $spreadsheet);
    

                if (!$isValid) {
                    $this->swal('error', 'Invalid File! Please download the template');
                    return redirect()->to(base_url() . '/customers/upload_form');
                }

    
                $sheetNames = $spreadsheet->getSheetNames();
                $sheet1Data = $spreadsheet->getSheet(0)->toArray();
                $sheet2Data = null;
                $sheet3Data = null;
                $sheet4Data = null;
                
              
                if (count($sheet1Data) <= 1) {
                    $this->swal('error', 'Empty File');
                    return redirect()->to(base_url() . '/customers/upload_form');
                } else{
                    if(count($sheetNames) == 4){
                        $sheet2Data = $spreadsheet->getSheet(1)->toArray();
                        $sheet3Data = $spreadsheet->getSheet(2)->toArray();
                        $sheet4Data = $spreadsheet->getSheet(3)->toArray();
                    } else{
                        $this->swal('error', 'Invalid File! Please download the template');
                        return redirect()->to(base_url() . '/customers/upload_form');
                    }
                }
                
                $newName = strtotime(time()) . $file->getRandomName();
                @$isload = $file->move( WRITEPATH . 'uploads/customers/', $newName);
                
                if ($sheet1Data) {
                    $validatedData = [];
                    $isValid = true;
                    $cellNumber = 1;
    
                    $custcode = null;
                    $regname = null;
                    $tradename = null;
                    $tinno = null;

                    $zip = null;

                    $class = null;
                    $type = null;
                    $creditlimit = null;
                    $salesman = null;

                    $arcode = null;
                    $accttitle = null;
                    $pricever = null;
                    $terms = null;
                    $currency = null;
                    $salestaxtype = null;
                   
                    $CustCodesSheet1 = [];
    
                    foreach ($sheet1Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            $headers = array_map('strval', $row);
                            foreach ($headers as $header) {
                                if (stripos($header, 'Customer Code') !== false)            {  $custcode = $header;     }
                                if (stripos($header, 'Registered Name') !== false)          {  $regname = $header;      }
                                if (stripos($header, 'Business / Trade Name') !== false)    {  $tradename = $header;    }
                                if (stripos($header, 'Tin No') !== false)                   {  $tinno = $header;        }
                                if (stripos($header, 'Zip Code') !== false)                 {  $zip = $header;          }
                                if (stripos($header, 'Types') !== false && stripos($header, '(From Customer Types)') !== false)                   {  $type = $header;         }
                                if (stripos($header, 'Classification') !== false)           {  $class = $header;        }
                                if (stripos($header, 'Credit Limit') !== false)             {  $creditlimit = $header;  }
                                if (stripos($header, 'Salesman') !== false)                 {  $salesman = $header;     }
                                if (stripos($header, 'AR Code') !== false && stripos($header, '(Single / Multiple)') !== false)                   {  $arcode = $header;       }
                                if (stripos($header, 'Account Title') !== false)            {  $accttitle = $header;    }
                                if (stripos($header, 'Price Version') !== false)            {  $pricever = $header;     }
                                if (stripos($header, 'Terms') !== false)                    {  $terms = $header;        }
                                if (stripos($header, 'Default Currency') !== false && stripos($header, '(From Currency Rate)') !== false)         {  $currency = $header;     }
                                if (stripos($header, 'Default Sales Tax Type') !== false)   {  $salestaxtype = $header; }
                            }

                            continue;
                        }
    
                        $rowErrors = [];
                        $rowData = array_combine($headers, array_map('trim', $row));
                        

                        if (!empty($rowData[$custcode])){
                            $custCodesSheet1[] = $rowData[$custcode];
                        } else{
                            $this->swal('error', 'Empty Customer Code!');
                            return redirect()->to(base_url() . '/customers/upload_form');
                        }
                        
                        $cellNumber++;
                        $rowData['Cell Number'] = $cellNumber;
                        $CustomerModel = $this->customerModel;
                     
                        // Validation
                        if (empty($rowData[$custcode]) || empty($rowData[$regname]) || empty($rowData[$tradename]) || empty($rowData[$tinno])  || empty($rowData[$arcode])) {
                            $rowErrors[] = '* Required fields must be filled';
                        }

                        $duplicateCustomer = $CustomerModel->where('cempid', $rowData[$custcode])->where('deleted', 0)->where('compcode', $this->company_code)->first();
                        if ($duplicateCustomer) {
                            $rowErrors[] = '* Customer code must be unique';
                        }
                        if (!empty($rowData[$zip])){
                            if (!is_numeric($rowData[$zip])) {
                                $rowErrors[] = '* Zip Code must be numeric';
                            }
                        }
                        if (!empty($rowData[$type])) {
                            $types = $this->db->table('customer_type')->where('ccode', $rowData[$type])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($types)) {
                                $rowErrors[] = '* Type must exist';
                            }
                        }
                        if (!empty($rowData[$class])) {
                            $classs = $this->db->table('customer_classification')->where('ccode', $rowData[$class])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($classs)) {
                                $rowErrors[] = '* Classification must exist';
                            }
                        }
                        if (!empty($rowData[$creditlimit])) {
                            try {
                                if (!is_numeric($rowData[$creditlimit]) && !is_float($rowData[$creditlimit] + 0)) {
                                    $rowErrors[] = '* Credit Limit must be numeric or float';
                                }
                            } catch (\Exception $e) {
                                $rowErrors[] = '* Credit Limit must be numeric or float';
                            }
                        }
                        if (!empty($rowData[$salesman])) {
                            $salesmans = $this->db->table('salesman')->where('ccode', $rowData[$salesman])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($salesmans)) {
                                $rowErrors[] = '* Salesman must exist';
                            }
                        }
                        if (!empty($rowData[$arcode])) {
                            $value = strtolower($rowData[$arcode]);
                            if ($value !== 'single' && $value !== 'multiple') {
                                $rowErrors[] = '* AR Code must be Single / Multiple';
                            } elseif($value === 'single') {
                                if(!empty($rowData[$accttitle])){
                                    $accttitles = $this->db->table('accounts')->where('cacctno', $rowData[$accttitle])->where('deleted', 0)->where('ctype', 'Details')->where('company_code', $this->company_code)->get()->getRow();
                                    if (empty($accttitles)) {
                                        $rowErrors[] = '* Account Title must exist';
                                    }
                                }else{
                                    $rowErrors[] = '* Account Title is required';
                                }
                            }
                        }
                        if (!empty($rowData[$pricever])) {
                            $pricevers = $this->db->table('groupings')->where('ccode', $rowData[$pricever])->where('deleted', 0)->where('ctype','ITMPMVER')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($pricevers)) {
                                $rowErrors[] = '* Price Version must exist';
                            }
                        }
                        if (!empty($rowData[$terms])) {
                            $termss = $this->db->table('groupings')->where('ccode', $rowData[$terms])->where('deleted', 0)->where('ctype','TERMS')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($termss)) {
                                $rowErrors[] = '* Terms must exist';
                            }
                        }
                        if (!empty($rowData[$currency])) {
                            $currencys = $this->db->table('currency_rate')->where('symbol', $rowData[$currency])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($currencys)) {
                                $rowErrors[] = '* Default Currency must exist';
                            }
                        }
                        if (!empty($rowData[$salestaxtype])) {
                            $salestaxtypes = $this->db->table('tax_types')->where('cvatcode', $rowData[$salestaxtype])->where('deleted', 0)->where('ctype','Sales')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($salestaxtypes)) {
                                $rowErrors[] = '* Default Sales Tax Type must exist';
                            }
                        }

                        // Check for duplicate customers codes within the current import
                        foreach ($validatedData as $validatedIndex => $validatedRow) {
                            if ($validatedRow[$custcode] === $rowData[$custcode]) {
                                $rowErrors[] = '* Duplicate customer code within the import file at cell number ' . ($validatedRow['Cell Number']); // Adding 1 to cell number to match Excel numbering
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
                    $data['loaded_page'] = 'Customers/PreviewCustomers';
                }

                if ($sheet2Data){
                    $validatedData2 = [];
                    $isValid2 = true;
                    $cellNumber2 = 1;
                    
                    $custcode2 = null;
                    $houseno = null;
                    $city = null;
                    $state = null;
                    $country = null;
                    $zipcode = null;

                    foreach ($sheet2Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header) {
                                if (stripos($header, 'Customer Code') !== false) {
                                    $custcode2 = $header;
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
                        if (empty($rowData[$custcode2]) || empty($rowData[$houseno]) || empty($rowData[$city]) || empty($rowData[$state]) || empty($rowData[$country])) {
                            $rowErrors2[] = '* Required fields must be filled';
                        }

                        if (!empty($rowData[$zipcode])){
                            if (!is_numeric($rowData[$zipcode])) {
                                $rowErrors2[] = '* Zip Code must be numeric';
                            }
                        }

                        $customercode = $this->db->table('customers')->where('cempid', $rowData[$custcode2])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                        
                        if (!in_array($rowData[$custcode2], $custCodesSheet1) && empty($customercode)) {
                            $rowErrors2[] = '* Customer code does not exist in the uploaded file and in Customers Table';

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
                    $data['loaded_page'] = 'Customers/PreviewCustomers';
                }

                if ($sheet3Data){
                    $validatedData3 = [];
                    $isValid3 = true;
                    $cellNumber3 = 1;
                    
                    $custcode3 = null;
                    $secname3 = null;
                    $houseno3 = null;
                    $city3 = null;
                    $state3 = null;
                    $country3 = null;
                    $zipcode3 = null;
                    $tinno3 = null;

                    foreach ($sheet3Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header) {
                                if (stripos($header, 'Customer Code') !== false) {
                                    $custcode3 = $header;
                                }
                                if (stripos($header, 'Secondary Customer Name') !== false) {
                                    $secname3 = $header;
                                }
                                if (stripos($header, 'House No. / Building No. / Street') !== false) {
                                    $houseno3 = $header;
                                }
                                if (stripos($header, 'City') !== false) {
                                    $city3 = $header;
                                }
                                if (stripos($header, 'State') !== false) {
                                    $state3 = $header;
                                }
                                if (stripos($header, 'Country') !== false) {
                                    $country3 = $header;
                                }
                                if (stripos($header, 'Zip Code') !== false) {
                                    $zipcode3 = $header;
                                }
                                if (stripos($header, 'Tin No') !== false) {
                                    $tinno3 = $header;
                                }
                            }
                            continue;
                        }

                        $rowErrors3 = [];
                        $rowData = array_combine($headers, array_map('trim', $row)); 
                        $cellNumber3++; 
                        $rowData['Cell Number'] = $cellNumber3;

                        // Validation
                        if (empty($rowData[$custcode3]) || empty($rowData[$secname3]) || empty($rowData[$houseno3]) || empty($rowData[$city3]) || empty($rowData[$state3]) || empty($rowData[$country3]) || empty($rowData[$tinno3])) {
                            $rowErrors3[] = '* Required fields must be filled';
                        }

                        if (!empty($rowData[$zipcode3])){
                            if (!is_numeric($rowData[$zipcode3])) {
                                $rowErrors3[] = '* Zip Code must be numeric';
                            }
                        }

                        $customercode3 = $this->db->table('customers')->where('cempid', $rowData[$custcode3])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                        
                        if (!in_array($rowData[$custcode3], $custCodesSheet1) && empty($customercode3)) {
                            $rowErrors3[] = '* Customer code does not exist in the uploaded file and in Customers Table';
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
                    $data['loaded_page'] = 'Customers/PreviewCustomers';
                }

                if ($sheet4Data){
                    $validatedData4 = [];
                    $isValid4 = true;
                    $cellNumber4 = 1;
                    
                    $custcode4 = null;
                    $contname3 = null;
                    $desig = null;
                    $dept = null;
                    $email4 = null;
                    $mobile4 = null;

                    foreach ($sheet4Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header) {
                                if (stripos($header, 'Customer Code') !== false) {
                                    $custcode4 = $header;
                                }
                                if (stripos($header, 'Customer Contact Name') !== false) {
                                    $contname3 = $header;
                                }
                                if (stripos($header, 'Designation') !== false) {
                                    $desig = $header;
                                }
                                if (stripos($header, 'Department') !== false) {
                                    $dept = $header;
                                }
                                if (stripos($header, 'Email Address') !== false) {
                                    $email4 = $header;
                                }
                                if (stripos($header, 'Mobile Number') !== false) {
                                    $mobile4 = $header;
                                }
                            }
                            continue;
                        }

                        $rowErrors4 = [];
                        $rowData = array_combine($headers, array_map('trim', $row)); 
                        $cellNumber4++; 
                        $rowData['Cell Number'] = $cellNumber4;

                        // Validation
                        if (empty($rowData[$custcode4]) || empty($rowData[$contname3]) || empty($rowData[$desig]) || empty($rowData[$dept]) || empty($rowData[$email4]) || empty($rowData[$mobile4])) {
                            $rowErrors4[] = '* Required fields must be filled';
                        }
                        
                        $customercode4 = $this->db->table('customers')->where('cempid', $rowData[$custcode4])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                        
                        if (!in_array($rowData[$custcode4], $custCodesSheet1) && empty($customercode4)) {
                            $rowErrors4[] = '* Customer code does not exist in the uploaded file and in Customers Table';
                        }
                        if (!empty($rowData[$email4])){
                            if (!filter_var($rowData[$email4], FILTER_VALIDATE_EMAIL)) {
                                $rowErrors4[] = '* Invalid email address';
                            }
                        }
                        if (!empty($rowData[$mobile4])){
                            if (!is_numeric($rowData[$mobile4])) {
                                $rowErrors4[] = '* Mobile Number must be numeric';
                            }
                        }

                        $rowData['errors4'] = $rowErrors4;
                        $validatedData4[] = $rowData;

                        if (!empty($rowErrors4)) {
                            $isValid4 = false;
                        }
                    }
                    
                    $data['data4'] = $validatedData4;
                    $data['isValid4'] = $isValid4;
                    $data['sheetName4'] = $sheetNames[3];
                    $data['loaded_page'] = 'Customers/PreviewCustomers';
                }    
            }
            
            return view($this->view . 'preview_customers', $data);
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

    public function insertCustomers()
    {
        $data = json_decode($this->request->getPost('tableData'))->table1;
        //$data = $this->request->getPost('data');
        $data2 = $this->request->getPost('data2');
        $data3 = $this->request->getPost('data3');
        $data4 = $this->request->getPost('data4');

        $success = false;
        $success2 = true; 
        $success3 = true; 
        $success4 = true; 

        if ($data) {
            $success = $this->inserttblDataCustomers($data);
        }
        if ($data2) {
            $success2 = $this->inserttblAddress($data2);
        }
        if ($data3) {
            $success3 = $this->insertSecondaryCust($data3);
        }
        if ($data4) {
            $success4 = $this->insertContactList($data4);
        }

        if ($success && $success2 && $success3 && $success4) {
            $this->swal('success', 'Successfully Inserted');
            return redirect()->to(base_url() . '/customers');
        } else {
            $this->swal('error', 'Insertion Failed');
            return redirect()->to(base_url() . '/customers');
        }
    }


    private function inserttblDataCustomers($data)
    {  
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'cempid' => 'Customer Code',
            'cname' => 'Registered Name',
            'ctradename' => 'Business / Trade Name',
            'ctin' => 'Tin No',

            'chouseno' => 'House No. / Building No. / Street',
            'ccity' => 'City',
            'cstate' => 'State',
            'ccountry' => 'Country',
            'czip' => 'Zip Code',

            'ccustomertype' => 'Customer Types',
            'ccustomerclass' => 'Classification',
            'nlimit' => 'Credit Limit',
            'csman' => 'Salesman',

            'cacctcodetype' => 'Single / Multiple',
            'cacctcodesales' => 'Account Title',
            
            'cpricever' => 'Price Version',
            'cterms' => 'Terms',
            'cdefaultcurrency' => 'Default Currency',
            'cvattype' => 'Default Sales Tax Type',
    
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
                        } else if (in_array($dbField, ['cacctcodetype'])) {
                            $rowData[$dbField] = strtolower($value);
                        }else {
                            $rowData[$dbField] = $value;
                        }
                    }
                }
            }

            $rowData['compcode'] = $this->company_code;
            $rowData['created_by'] = $this->user_id;
            $rowData['created_date'] = date("Y-m-d H:i:s");

            $logfile = array(
                'user_id' => $this->user_id,
                'created_by' => $this->user_id,
                'created_date' => date("Y-m-d H:i:s"),
                'main_module' => 'Administrator',
                'sub_module' => 'Customers > Masterlist',
                'event' => 'MASS UPLOAD MASTERLIST',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
           
            if (!empty($rowData['cempid']) && !empty($rowData['cname'])) {
                //$this->db->table('logfile_customer_masterfile')->insert($logfile);
                $saveSuccess = $this->customerModel->insert($rowData);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;}
            }
        return $success;
    }

    private function inserttblAddress($data)
    {   
    
        $fieldMappings = [
            'ccode' => 'Customer Code',
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
                'sub_module' => 'Customers > Masterfile',
                'event' => 'MASS UPLOAD ADDRESS',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['ccode'])) {
                $this->db->table('logfile_customer_masterfile')->insert($logfile); 
                $saveSuccess = $this->db->table('customers_address')->insert($rowData);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }
    
        return $success;
    }

    private function insertSecondaryCust($data)
    {   
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'cmaincode' => 'Customer Code',
            'cname' => 'Secondary Customer Name',
            'caddress' => 'House No. / Building No. / Street',
            'ccity' => 'City',
            'cstate' => 'State',
            'ccountry' => 'Country',
            'czip' => 'Zip Code',
            'ctin' => 'Tin No',
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
                        } elseif (in_array($dbField, ['cmaincode'])) {
                            $rowData[$dbField] = $value;
                            $secondcust = $this->db->table('customers_secondary')->selectMax('norder')
                            ->where('cmaincode', $value)->where('compcode', $this->company_code)->get()->getRow();
                                $seccount = '';
                    
                                if (!empty($secondcust->norder)) {
                                    $seccount = $secondcust->norder;
                                } else {
                                    $seccount = '0000';
                                } 
                                $seccount++;
					            $formattedSeccount = sprintf('%04d', $seccount); // Format $seccount with leading zeros
                                $rowData['norder'] = $seccount;
                                $rowData['ccode'] = $value . "-" . $formattedSeccount;
                                             
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
                'sub_module' => 'Customers > Masterfile',
                'event' => 'MASS UPLOAD SECONDARY ADDRESS',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['cmaincode'])) {
                $this->db->table('logfile_customer_masterfile')->insert($logfile); 
                $saveSuccess = $this->db->table('customers_secondary')->insert($rowData);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }
    
        return $success;
    }

    private function insertContactList($data)
    {   
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'ccode' => 'Customer Code',
            'cname' => 'Customer Contact Name',
            'cdesignation' => 'Designation',
            'cdept' => 'Department',
            'cemail' => 'Email Address',
            'cmobile' => 'Mobile Number',
            'cphone' => 'Landline Number',
        ];

        // Initialize success variable
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
                'sub_module' => 'Customers > Masterfile',
                'event' => 'MASS UPLOAD CONTACT LIST',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['ccode'])) {
                $this->db->table('logfile_customer_masterfile')->insert($logfile); 
                $saveSuccess = $this->db->table('customers_contacts')->insert($rowData);
                
                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }
        return $success;
    }

    public function downloadTemplate()
    {
        $filePath = WRITEPATH . 'templates/customertemplate.xlsx'; // Path to your file in the writable directory
        return $this->response->download($filePath, null);
    }

}