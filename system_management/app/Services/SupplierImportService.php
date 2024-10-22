<?php

namespace App\Services;

use App\Models\Suppliers\SuppliersModel;
use App\Entities\Suppliers\SuppliersEntity;
use CodeIgniter\Database\ConnectionInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SupplierImportService
{
    private $db;
    private $suppliersModel;
    private $company_code;
    private $user_id;

    public function __construct(ConnectionInterface $db, SuppliersModel $suppliersModel)
    {
        $this->db = $db;
        $this->suppliersModel = $suppliersModel;
        $this->company_code = session()->get('current_company')->company_code;
        $this->user_id = session()->get('user_id');
    }

    public function processSpreadsheet(Spreadsheet $spreadsheet): array
    {
        $sheetNames = $spreadsheet->getSheetNames();
        $data = [
            'data' => $this->processSheet($spreadsheet->getSheet(0), 'Supplier Code'),
            'data2' => $this->processSheet($spreadsheet->getSheet(1), 'Supplier Code'),
            'data3' => $this->processSheet($spreadsheet->getSheet(2), 'Supplier Code'),
            'isValid' => true,
            'isValid2' => true,
            'isValid3' => true,
            'sheetName1' => $sheetNames[0],
            'sheetName2' => $sheetNames[1],
            'sheetName3' => $sheetNames[2],
            'loaded_page' => 'Suppliers/PreviewSuppliers'
        ];

        foreach (['data', 'data2', 'data3'] as $key) {
            if (!empty($data[$key])) {
                $data['isValid' . substr($key, -1)] = !array_reduce($data[$key], function($carry, $item) {
                    return $carry || !empty($item['errors']);
                }, false);
            }
        }

        return $data;
    }

    private function processSheet($sheet, $codeColumn): array
    {
        $data = $sheet->toArray();
        $headers = array_shift($data);
        $validatedData = [];

        foreach ($data as $index => $row) {
            $rowData = array_combine($headers, $row);
            $rowData['errors'] = $this->validateRow($rowData, $codeColumn);
            $rowData['Cell Number'] = $index + 2;
            $validatedData[] = $rowData;
        }

        return $validatedData;
    }

    private function validateRow($rowData, $codeColumn): array
    {
        $errors = [];

        if (empty($rowData[$codeColumn])) {
            $errors[] = '* Required fields must be filled';
        }

        // Add more validation rules here based on your requirements

        return $errors;
    }

    public function insertSuppliers($data, $data2, $data3): bool
    {
        $this->db->transStart();

        $success = $this->insertSupplierData($data);
        $success &= $this->insertSupplierAddresses($data2);
        $success &= $this->insertSupplierContacts($data3);

        $this->db->transComplete();

        return $success && $this->db->transStatus();
    }

    private function insertSupplierData($data): bool
    {
        foreach ($data as $row) {
            $supplier = new SuppliersEntity($this->mapSupplierData($row));
            if (!$this->suppliersModel->insert($supplier)) {
                return false;
            }
        }
        return true;
    }

    private function insertSupplierAddresses($data): bool
    {
        foreach ($data as $row) {
            $addressData = $this->mapAddressData($row);
            if (!$this->db->table('suppliers_address')->insert($addressData)) {
                return false;
            }
        }
        return true;
    }

    private function insertSupplierContacts($data): bool
    {
        foreach ($data as $row) {
            $contactData = $this->mapContactData($row);
            if (!$this->db->table('suppliers_contacts')->insert($contactData)) {
                return false;
            }
        }
        return true;
    }

    private function mapSupplierData($row): array
    {
        // Map row data to supplier fields
        // This is a simplified example, adjust according to your actual data structure
        return [
            'ccode' => $row['Supplier Code'],
            'cname' => strtoupper($row['Registered Name']),
            'ctradename' => strtoupper($row['Business / Trade Name']),
            'ctin' => $row['Tin No'],
            // ... map other fields ...
            'compcode' => $this->company_code,
            'created_by' => $this->user_id,
            'created_date' => date('Y-m-d H:i:s'),
        ];
    }

    private function mapAddressData($row): array
    {
        // Map row data to address fields
        return [
            'ccode' => $row['Supplier Code'],
            'chouseno' => $row['House No. / Building No. / Street'],
            'ccity' => strtoupper($row['City']),
            'cstate' => strtoupper($row['State']),
            'ccountry' => strtoupper($row['Country']),
            'czip' => $row['Zip Code'],
            'compcode' => $this->company_code,
        ];
    }

    private function mapContactData($row): array
    {
        // Map row data to contact fields
        return [
            'ccode' => $row['Supplier Code'],
            'cname' => $row['Supplier Contact Name'],
            'cdesignation' => $row['Designation'],
            'cdept' => $row['Department'],
            'cemail' => $row['Email Address'],
            'cmobile' => $row['Mobile Number'],
            'cphone' => $row['Landline Number'] ?? null,
            'compcode' => $this->company_code,
        ];
    }
}