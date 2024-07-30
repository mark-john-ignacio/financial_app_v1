<?php

namespace App\Controllers\Items;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\Files\File;
use App\Models\Items\ItemsModel;
use App\Entities\Items\ItemsEntity;

class ItemsController extends BaseController
{
    protected $itemsModel;
    protected $view;
    protected $company_code;
    protected $user_id;
    private ItemsEntity $itemsEntity;

    public function __construct()
    {
        $this->itemsModel = new ItemsModel();
        $this->view = 'Items/';
        $this->company_code = session()->get('current_company')->company_code;
        $this->user_id = session()->get('user_id');
        $this->db = \Config\Database::connect();
        $this->itemsEntity = new ItemsEntity();

    }
    public function index()
    {
        $data = [
            'title' => 'Items'
        ];
        return view($this->view . 'index', $data);
    }

    public function load()
    {
        $items = $this->itemsModel->findAll();
        return $this->response->setJSON($items);
    }

    public function upload_form()
    {
        $data = [
            'errors' => [],
            'title' => 'Upload Items'
        ];
        return view($this->view . 'upload_form', $data);
    }

    public function downloadTemplate()
    {
        $filePath = WRITEPATH . 'templates/items-template.xlsx'; // Path to your file in the writable directory
        return $this->response->download($filePath, null);
    }

    public function upload()
    {
        $data['script'] = ['datatable'];
        $data['form_title'] = 'Import Items';
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
                return redirect()->to(url_to("items-upload-form"));
            } else {
                $file = $this->request->getFile('userfile');
                $spreadsheet = IOFactory::load($file->getTempName());

                $template = WRITEPATH . 'templates/items-template.xlsx';
                $templateSpreadsheet = IOFactory::load($template);

                // Compare the structure
                $isValid = $this->compareExcelStructure($templateSpreadsheet, $spreadsheet);

                if (!$isValid) {
                    $this->swal('error', 'Invalid File! Please download the template');
                    return redirect()->to(url_to("items-upload-form"));
                }

    
                $sheetNames = $spreadsheet->getSheetNames();
                $sheet1Data = $spreadsheet->getSheet(0)->toArray();
                $sheet2Data = null;
                $sheet3Data = null;
              
              
                if (count($sheet1Data) <= 1) {
                    $this->swal('error', 'Empty File');
                    return redirect()->to(url_to("items-upload-form"));
                } else{
                    if(count($sheetNames) == 3){
                        $sheet2Data = $spreadsheet->getSheet(1)->toArray();
                        $sheet3Data = $spreadsheet->getSheet(2)->toArray();
                    } else{
                        $this->swal('error', 'Invalid File! Please Download the Template');
                        return redirect()->to(url_to("items-upload-form"));
                    }
                }
                $newName = strtotime(time()) . $file->getRandomName();
                @$isload = $file->move( WRITEPATH . 'uploads/items/', $newName);
    
                if ($sheet1Data) {
                    $validatedData = [];
                    $isValid = true;
                    $cellNumber = 1;
    
                    $itemcode = null;
                    $description = null;
                    $uom = null;
                    $class = null;
                    $type = null;
                    $sales = null;
                    $trade = null;
                    $salestype = null;
                    $purchase = null;
                    $salesar = null;
                    $salesret = null;
                    $receive = null;
                    $dr = null;
                    $cog = null;
                    $serial = null;
                    $barcode = null;
                    $inventoriable = null;
                   
                    $itemCodesSheet1 = [];
    
                    foreach ($sheet1Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            $headers = array_map('strval', $row);
                            foreach ($headers as $header) {
                                if (stripos($header, 'Item Code') !== false)         {  $itemcode = $header;    }
                                if (stripos($header, 'Description') !== false)       {  $description = $header; }
                                if (stripos($header, 'Unit of Measure') !== false)   {  $uom = $header;         }
                                if (stripos($header, 'Classification') !== false)    {  $class = $header;       }
                                if (stripos($header, 'Types') !== false && stripos($header, '(From Item Types)') !== false)             {  $type = $header;        }
                                if (stripos($header, 'Trade Type') !== false)        {  $trade = $header;       }
                                if (stripos($header, 'Sales Type') !== false)        {  $salestype = $header;   }
                                if (stripos($header, 'Sales Tax Type') !== false)    {  $sales = $header;       }
                                if (stripos($header, 'Purchase Tax Type') !== false && stripos($header, '(From Tax Types)') !== false) {  $purchase = $header;    }
                                if (stripos($header, 'Sales AR') !== false)          {  $salesar = $header;     }
                                if (stripos($header, 'Sales Return') !== false)      {  $salesret = $header;    }
                                if (stripos($header, 'Receiving AP') !== false)      {  $receive = $header;     }
                                if (stripos($header, 'DR') !== false)                {  $dr = $header;          }
                                if (stripos($header, 'Cost of Goods') !== false)     {  $cog = $header;         }
                                if (stripos($header, 'Serial No') !== false)         {  $serial = $header;      }
                                if (stripos($header, 'Barcode') !== false)           {  $barcode = $header;     }
                                if (stripos($header, 'Non-Inventoriable') !== false) {  $inventoriable = $header;         }
                            }

                            continue;
                        }
    
                        $rowErrors = [];
                        $rowData = array_combine($headers, array_map('trim', $row));

                        if (!empty($rowData[$itemcode])){
                            $itemCodesSheet1[] = $rowData[$itemcode];
                        } else{
                            $this->swal('error', 'Empty Customer Code!');
                            return redirect()->to(url_to("items-upload-form"));
                        }

                        $cellNumber++;
                        $rowData['Cell Number'] = $cellNumber;
                        $itemsModel = $this->itemsModel;
                     
                        // Validation
                        if (empty($rowData[$itemcode]) || empty($rowData[$description])) {
                            $rowErrors[] = '* Required fields must be filled';
                        }

                        $duplicateItems = $itemsModel->where('cpartno', $rowData[$itemcode])->where('deleted', 0)->where('compcode', $this->company_code)->first();
                        if ($duplicateItems) {
                            $rowErrors[] = '* Item code must be unique';
                        }
                        if (!empty($rowData[$uom])) {
                            $uoms = $this->db->table('groupings')->where('ccode', $rowData[$uom])->where('cstatus', 'ACTIVE')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($uoms)) {
                                $rowErrors[] = '* Unit of Measure must exist';
                            }
                        }
                        if (!empty($rowData[$class])) {
                            $classs = $this->db->table('groupings')->where('ccode', $rowData[$class])->where('cstatus', 'ACTIVE')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($classs)) {
                                $rowErrors[] = '* Classification must exist';
                            }
                        }
                        if (!empty($rowData[$type])) {
                            $types = $this->db->table('groupings')->where('ccode', $rowData[$type])->where('cstatus', 'ACTIVE')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($types)) {
                                $rowErrors[] = '* Type must exist';
                            }
                        }
                        if (!empty($rowData[$trade])) {
                            $value = strtolower($rowData[$trade]);
                            if ($value !== 'trade' && $value !== 'non-trade') {
                                $rowErrors[] = '* Trade Type must be Trade / Non-Trade';
                            }
                        }
                        if (!empty($rowData[$salestype])) {
                            $value = strtolower($rowData[$salestype]);
                            if ($value !== 'services' && $value !== 'goods') {
                                $rowErrors[] = '* Sales Type must be Goods / Services';
                            }
                        }
                        if (!empty($rowData[$sales])) {
                            $saless = $this->db->table('vatcode')->where('cvatcode', $rowData[$sales])->where('ctype','Sales')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($saless)) {
                                $rowErrors[] = '* Sales Tax Type must exist';
                            }
                        }
                        if (!empty($rowData[$purchase])) {
                            $purchases = $this->db->table('vatcode')->where('cvatcode', $rowData[$purchase])->where('ctype','Purchase')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($purchases)) {
                                $rowErrors[] = '* Purchase Tax Type must exist';
                            }
                        }
                        if (!empty($rowData[$salesar])) {
                            $salesars = $this->db->table('accounts')->where('cacctno', $rowData[$salesar])->where('ctype','Details')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($salesars)) {
                                $rowErrors[] = '* Sales AR must exist';
                            }
                        }
                        if (!empty($rowData[$salesret])) {
                            $salesrets = $this->db->table('accounts')->where('cacctno', $rowData[$salesret])->where('ctype','Details')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($salesrets)) {
                                $rowErrors[] = '* Sales Return must exist';
                            }
                        }
                        if (!empty($rowData[$receive])) {
                            $receives = $this->db->table('accounts')->where('cacctno', $rowData[$receive])->where('ctype','Details')->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($receives)) {
                                $rowErrors[] = '* Receiving AP must exist';
                            }
                        }
                        if (!empty($rowData[$dr])) {
                            $drs = $this->db->table('accounts')->where('cacctno', $rowData[$dr])->where('deleted', 0)->where('ctype','Details')->where('company_code', $this->company_code)->get()->getRow();
                            if (empty($drs)) {
                                $rowErrors[] = '* DR must exist';
                            }
                        }
                        if (!empty($rowData[$cog])) {
                            $cogs = $this->db->table('accounts')->where('cacctno', $rowData[$cog])->where('deleted', 0)->where('ctype','Details')->where('company_code', $this->company_code)->get()->getRow();
                            if (empty($cogs)) {
                                $rowErrors[] = '* Cost of Goods must exist';
                            } 
                        }
                        if (!empty($rowData[$serial])) {
                            $value = strtolower($rowData[$serial]);
                            if ($value !== 'yes' && $value !== 'no') {
                                $rowErrors[] = '* Serial No. must be Yes / No';
                            }
                        }
                        if (!empty($rowData[$barcode])) {
                            $value = strtolower($rowData[$barcode]);
                            if ($value !== 'yes' && $value !== 'no') {
                                $rowErrors[] = '* Barcode must be Yes / No';
                            }
                        }
                        if (!empty($rowData[$inventoriable])) {
                            $value = strtolower($rowData[$inventoriable]);
                            if ($value !== 'yes' && $value !== 'no') {
                                $rowErrors[] = '* Non-Inventoriable must be Yes / No';
                            }
                        }
                    
                        // Check for duplicate item codes within the current import
                        foreach ($validatedData as $validatedIndex => $validatedRow) {
                            if ($validatedRow[$itemcode] === $rowData[$itemcode]) {
                                $rowErrors[] = '* Duplicate item code within the import file at cell number ' . ($validatedRow['Cell Number']); // Adding 1 to cell number to match Excel numbering
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
                    $data['loaded_page'] = 'admin/PreviewItems';
                }
    
                if (count($sheet2Data) > 1) {
                    $validatedData2 = [];
                    $isValid2 = true;
                    $cellNumber2 = 1;
    
                    $itemcode2 = null;
                    $section2 = null;
                    $min2 = null;
                    $max2 = null;
                    $reorder2 = null;
    
                    foreach ($sheet2Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header2) {
                                if (stripos($header2, 'Item Code') !== false) {
                                    $itemcode2 = $header2;
                                }
                                if (stripos($header2, 'Section') !== false) {
                                    $section2 = $header2;
                                }
                                if (stripos($header2, 'Minimum') !== false) {
                                    $min2 = $header2;
                                }
                                if (stripos($header2, 'Maximum') !== false) {
                                    $max2 = $header2;
                                }
                                if (stripos($header2, 'Reorder Point') !== false) {
                                    $reorder2 = $header2;
                                }
                            }
                            continue;
                        }
    
                        $rowErrors = [];
                        $rowData = array_combine($headers, array_map('trim', $row));
                        $cellNumber2++;
                        $rowData['Cell Number'] = $cellNumber2;

                        $itemcode22 = $itemsModel->where('cpartno', $rowData[$itemcode2])->where('deleted', 0)->where('compcode', $this->company_code)->first();
                        if (!in_array($rowData[$itemcode2], $itemCodesSheet1) && empty($itemcode22)) {
                            $rowErrors[] = '* Item code does not exist in the uploaded file and in Items Table';
                        }
                        if (empty($rowData[$itemcode2]) || empty($rowData[$section2])) {
                            $rowErrors[] = '* Required fields must be filled';
                        }
                        
                        // Validation
                        if (!empty($rowData[$section2])) {
                            $section22 = $this->db->table('locations')->where('nid', $rowData[$section2])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($section22)) {
                                $rowErrors[] = '* Section must exist';
                            }
                            if (!is_numeric($rowData[$section2])) {
                                $rowErrors[] = '* Section must be numeric';
                            }
                        }
                        if (!empty($rowData[$min2])) {
                            try{
                                if (!is_numeric($rowData[$min2]) && !is_float($rowData[$min2] + 0)) {
                                    $rowErrors[] = '* Minimum must be numeric or float';
                                }
                            } catch (\Exception $e) {
                                $rowErrors[] = '* Minimum must be numeric or float';
                            }
                        }
                        if (!empty($rowData[$max2])) {
                            try{
                                if (!is_numeric($rowData[$max2]) && !is_float($rowData[$max2] + 0)) {
                                    $rowErrors[] = '* Maximum must be numeric or float';
                                }
                            } catch (\Exception $e) {
                                $rowErrors[] = '* Maximum must be numeric or float';
                            }
                        }
                        if (!empty($rowData[$reorder2])) {
                            try{
                                if (!is_numeric($rowData[$reorder2]) && !is_float($rowData[$reorder2] + 0)) {
                                    $rowErrors[] = '* Reorder Point must be numeric or float';
                                }
                            } catch (\Exception $e) {
                                $rowErrors[] = '* Reorder Point must be numeric or float';
                            }
                        }   

                        $rowData['errors2'] = $rowErrors;
                        $validatedData2[] = $rowData;
    
                        if (!empty($rowErrors)) {
                            $isValid2 = false;
                        }
                    }
    
                    $data['data2'] = $validatedData2;
                    $data['isValid2'] = $isValid2;
                    $data['sheetName2'] = $sheetNames[1];
                    $data['loaded_page'] = 'admin/PreviewItems';
                }

                if (count($sheet3Data) > 1) {
                    $validatedData3 = [];
                    $isValid3 = true;
                    $cellNumber3 = 1;
    
                    $itemcode3 = null;
                    $uom3 = null;
                    $factor3 = null;
                    $rule3 = null;
                    $pouom3 = null;
                    $salesuom3 = null;
    
                    foreach ($sheet3Data as $index => $row) {
                        if ($index == 0) {
                            $headers = $row;
                            foreach ($headers as $header3) {
                                if (stripos($header3, 'Item Code') !== false) {
                                    $itemcode3 = $header3;
                                }
                                if (stripos($header3, 'Unit of Measure') !== false) {
                                    $uom3 = $header3;
                                }
                                if (stripos($header3, 'Factor') !== false) {
                                    $factor3 = $header3;
                                }
                                if (stripos($header3, 'Rule') !== false) {
                                    $rule3 = $header3;
                                }
                                if (stripos($header3, 'PO UOM') !== false) {
                                    $pouom3 = $header3;
                                }
                                if (stripos($header3, 'Sales UOM') !== false) {
                                    $salesuom3 = $header3;
                                }
                            }
                            continue;
                        }
    
                        $rowErrors = [];
                        $rowData = array_combine($headers, array_map('trim', $row));
                        $cellNumber3++; 
                        $rowData['Cell Number'] = $cellNumber3;

                        // Validation
                        if (empty($rowData[$itemcode3]) || empty($rowData[$uom3])) {
                            $rowErrors[] = '* Required fields must be filled';
                        }

                        $itemcode33 = $itemsModel->where('cpartno', $rowData[$itemcode3])->where('deleted', 0)->where('compcode', $this->company_code)->first();
                        if (!in_array($rowData[$itemcode3], $itemCodesSheet1) && empty($itemcode33)) {
                            $rowErrors[] = '* Item code does not exist in the uploaded file and in Items Table';
                        }
                        if (!empty($rowData[$uom3])){
                            $uom33 = $this->db->table('item_unit')->where('ccode', $rowData[$uom3])->where('deleted', 0)->where('compcode', $this->company_code)->get()->getRow();
                            if (empty($uom33)) {
                                $rowErrors[] = '* UOM must exist';
                            }
                        }
                        if (!empty($rowData[$factor3])) {
                            try {
                                if (!is_numeric($rowData[$factor3]) && !is_float($rowData[$factor3] + 0)) {
                                    $rowErrors[] = '* Factor must be numeric or float';
                                }
                            } catch (\Exception $e) {
                                $rowErrors[] = '* Factor must be numeric or float';
                            }
                        }
                        if (!empty($rowData[$rule3])) {
                            if ($rowData[$rule3] !== '>' && $rowData[$rule3] !== '<') {
                                $rowErrors[] = '* Rule must be > or <';
                            }
                        }
                        if (!empty($rowData[$pouom3])) {
                            $value = strtolower($rowData[$pouom3]);
                            if ($value !== 'yes' && $value !== 'no') {
                                $rowErrors[] = '* PO UOM must be Yes or No';
                            }
                        }
                        if (!empty($rowData[$salesuom3])) {
                            $value = strtolower($rowData[$salesuom3]);
                            if ($value !== 'yes' && $value !== 'no') {
                                $rowErrors[] = '* Sales UOM must be Yes or No';
                            }
                        }
    
                        $rowData['errors3'] = $rowErrors;
                        $validatedData3[] = $rowData;
    
                        if (!empty($rowErrors)) {
                            $isValid2 = false;
                        }
                    }
    
                    $data['data3'] = $validatedData3;
                    $data['isValid3'] = $isValid3;
                    $data['sheetName3'] = $sheetNames[2];
                    $data['loaded_page'] = 'admin/PreviewItems';
                }
                return view($this->view . 'upload_preview', $data);
            }
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
    

    public function insertItems()
    {
        $data = json_decode($this->request->getPost('tableData'))->table1;
        //$data = $this->request->getPost('data');
        $data2 = $this->request->getPost('data2');
        $data3 = $this->request->getPost('data3');

        $success = false;
        $success2 = true; 
        $success3 = true;

        if ($data) {
            $success = $this->inserttblDataItems($data);
        }
        if ($data2) {
            $success2 = $this->inserttblInvLevel($data2);
        }
        if ($data3) {
            $success3 = $this->inserttblFactor($data3);
        }
        if ($success && $success2 && $success3) {
            $this->swal('success', 'Successfully Inserted');
            return redirect()->to(site_url('items'));
        } else {
            $this->swal('error', 'Insertion Failed');
            return redirect()->to(site_url('items'));
        }

    }

    private function inserttblDataItems($data)
    {   
        $itemsModel = new ItemsModel();
    
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'cpartno' => 'Item Code',
            'cskucode' => 'SKU Code',
            'citemdesc' => 'Description',
            'cunit' => 'Unit of Measure',
            'cclass' => 'Classification',
            'ctype' => 'Type',
            'ctradetype' => 'Trade Type',
            'csalestype' => 'Sales Type',
    
            'ctaxcode' => 'Sales Tax Type',
            'cpurchtaxcode' => 'Purchase Tax Type',
            'cpricetype' => 'Item Pricing',
            'nmarkup' => 'Fix / Mark-Up %',
    
            'cacctcodesales' => 'Sales AR',
            'cacctcodewrr' => 'Receiving AP',
            'cacctcodedr' => 'DR',
            'cacctcoderet' => 'Sales Return',
            'cacctcodecog' => 'Cost of Goods',
    
            'cnotes' => 'Notes',
            'lSerial' => 'Serial No',
            'lbarcode' => 'Barcode',
            'linventoriable' => 'Non-Inventoriable',
        ];
    
        $success = true;
    
        foreach ($data as $row) {
            $rowData = [];
    
            // Loop through the field mappings
            foreach ($fieldMappings as $dbField => $keyword) {
                foreach ($row as $key => $value) {
                    if (stripos($key, $keyword) !== false) {
                        if ($dbField === 'lSerial' || $dbField === 'lbarcode' || $dbField === 'linventoriable') {
                            $rowData[$dbField] = strtolower($value) === 'yes' ? 1 : 0;
                        } else {
                            $rowData[$dbField] = $value;
                        }
                        break;
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
                'sub_module' => 'Items > Masterlist',
                'event' => 'MASS UPLOAD MASTERLIST',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['cpartno'])) {
                //$this->db->table('logfile_items_masterfile')->insert($logfile); 
                $item = new ItemsEntity($rowData);

                $saveSuccess = $itemsModel->insert($item);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }
    
        return $success;
    }

    private function inserttblInvLevel($data)
    {   
        $invlevel = new ItemsInvLevel();
    
        $fieldMappings = [
            'cpartno' => 'Item Code',
            'section_nid' => 'Section',
            'nmin' => 'Minimum',
            'nmax' => 'Maximum',
            'nreorderpt' => 'Reorder Point',
        ];
    
        $success = true;
    
        foreach ($data as $row) {
            $rowData = [];
    
            // Loop through the field mappings
            foreach ($fieldMappings as $dbField => $keyword) {
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
                'sub_module' => 'Items > Masterfile',
                'event' => 'MASS UPLOAD INVENTORY LEVEL',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['cpartno'])  && !empty($rowData['section_nid'])) {
                $this->db->table('logfile_items_masterfile')->insert($logfile); 
                $saveSuccess = $invlevel->save($rowData);

                if (!$saveSuccess) {
                    $success = false;
                }
            } else {
                $success = false;
            }
        }
    
        return $success;
    }

    private function inserttblFactor($data)
    {   
        $invfactor = new ItemsFactor();
    
        // Define the mapping of keywords to database fields
        $fieldMappings = [
            'cpartno' => 'Item Code',
            'cunit' => 'Unit of Measure',
            'nfactor' => 'Factor',
            'crule' => 'Rule',
            'lpounit' => 'PO UOM',
            'lsiunit' => 'Sales UOM',
        ];
    
        $success = true;
    
        foreach ($data as $row) {
            $rowData = [];
    
            // Loop through the field mappings
            foreach ($fieldMappings as $dbField => $keyword) {
                foreach ($row as $key => $value) {
                    if (stripos($key, $keyword) !== false) {
                        if ($dbField === 'lpounit' || $dbField === 'lsiunit') {
                            $rowData[$dbField] = strtolower($value) === 'yes' ? 1 : 0;
                            
                        } elseif ($dbField === 'crule') {
                            $rowData[$dbField] = strtolower($value) === '>' ? 'mul' : 'div';
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
                'sub_module' => 'Items > Masterfile',
                'event' => 'MASS UPLOAD FACTOR',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'date_time' => date("H:i:s", strtotime("now")),
                'value_to' => json_encode($rowData)
            );
    
            if (!empty($rowData['cpartno'])  && !empty($rowData['cunit'])) {
                $this->db->table('logfile_items_masterfile')->insert($logfile); 
                $saveSuccess = $invfactor->save($rowData);
                
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
        $this->itemsModel->where('compcode', $this->company_code)->delete();
        $this->db->query("ALTER TABLE items AUTO_INCREMENT = 1");
        $this->swal('success', 'Successfully Deleted All Items');
        return redirect()->to(site_url('items'));
    }
}
