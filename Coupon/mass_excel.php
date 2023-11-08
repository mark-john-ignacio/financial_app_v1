<?php

if (isset($_FILES['excel_file'])) {
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

                    $rowdata[] = trim($cellValue);
                }
                if ($hasNonNullValue) {
                    echo json_encode($rowdata);
                    // echo json_encode(array_filter($rowdata, function ($value) {
                    //     return $value;
                    // }));
                }
            }

            unlink($uploadedFile);
        } else {
            echo "Please upload a valid Excel file (XLSX or XLS format).";
        }
    } else {
        echo "Error uploading the file. Please try again.";
    }
}
?>