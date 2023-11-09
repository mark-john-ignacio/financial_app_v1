<?php

$excel_data = [];

if (isset($_FILES['excel_file']) && !empty($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    if ($file['error'] === 0) {
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (in_array($fileExt, ['xlsx', 'xls'])) {
            $uploadDir = './';
            $uploadedFile = $uploadDir . $file['name'];
            move_uploaded_file($file['tmp_name'], $uploadedFile);

            require '../vendor2/autoload.php';

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadedFile);
            $worksheet = $spreadsheet->getActiveSheet();

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $rowdata = [];

                $hasNonNullValue = false;

                foreach ($cellIterator as $cell) {
                    $cellValue = $cell->getValue();

                    if (!is_null($cellValue)) {
                        $hasNonNullValue = true;
                    }

                    // Check if the cell can be converted to a date
                    if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                        $formattedDate = $date->format('Y-m-d'); // Adjust the format as needed
                        $rowdata[] = $formattedDate;
                    } else {
                        $rowdata[] = trim($cellValue);
                    }
                }

                if ($hasNonNullValue) {
                    // echo json_encode($rowdata);
                    array_push($excel_data, $rowdata);
                }
            }

            unlink($uploadedFile);
        } else {
            $proceed = false;
        }
    } else {
        $proceed = false;
    }
}

echo json_encode([
    'valid' => true,
    'data' => $excel_data
]);