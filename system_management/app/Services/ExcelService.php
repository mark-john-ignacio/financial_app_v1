<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelService
{
    public function loadSpreadsheet($file): Spreadsheet
    {
        return IOFactory::load($file->getTempName());
    }

    public function isValidTemplate(Spreadsheet $uploadedSpreadsheet): bool
    {
        $templatePath = WRITEPATH . 'templates/suppliers-template.xlsx';
        $templateSpreadsheet = IOFactory::load($templatePath);

        return $this->compareExcelStructure($templateSpreadsheet, $uploadedSpreadsheet);
    }

    private function compareExcelStructure(Spreadsheet $templateSpreadsheet, Spreadsheet $uploadedSpreadsheet): bool
    {
        $templateSheetNames = $templateSpreadsheet->getSheetNames();
        $uploadedSheetNames = $uploadedSpreadsheet->getSheetNames();

        if (count($templateSheetNames) !== count($uploadedSheetNames)) {
            return false;
        }

        foreach ($templateSheetNames as $index => $templateSheetName) {
            if ($templateSheetName !== $uploadedSheetNames[$index]) {
                return false;
            }

            $templateSheet = $templateSpreadsheet->getSheet($index)->toArray();
            $uploadedSheet = $uploadedSpreadsheet->getSheet($index)->toArray();

            if (count($templateSheet[0]) !== count($uploadedSheet[0])) {
                return false;
            }

            for ($i = 0; $i < count($templateSheet[0]); $i++) {
                if ($templateSheet[0][$i] !== $uploadedSheet[0][$i]) {
                    return false;
                }
            }
        }

        return true;
    }
}