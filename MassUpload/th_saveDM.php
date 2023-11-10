<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $proceed = true;
    $duplicate = false;
    $isFinished = false;


    /**
     * Initiate Variable
     */
    $label = $_POST['label'];
    $description = $_POST['description'];
    $effect = date("Y-m-d", strtotime($_POST['effectdate']));
    $due = date("Y-m-d", strtotime($_POST['duedate']));

    $month = date('m');
    $year = date('y');

    $sql = "SELECT * FROM discountmatrix where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By `tranno` desc LIMIT 1";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query)==0) {
        $code = "DM".$month.$year."00000";
    } else {
        while($row = $query -> fetch_assoc()){
            $last = $row['tranno'];
        }
        
        
        if(substr($last,2,2) <> $month){
            $code = "DM".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,6,5)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $code = "DM".$month.$year.$baseno;
        }
    }

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
                        $cellValue = trim($cell->getValue());

                        if (!is_null($cellValue)) {
                            $hasNonNullValue = true;
                        }

                        // Check if the cell can be converted to a date
                        if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($cellValue);
                            $formattedDate = $date->format('Y-m-d');    
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

    if($proceed){
        for($i = 1; $i < sizeof($excel_data); $i++){
            $data = $excel_data[$i];

            if($i === 1){
                $sql = "INSERT INTO discountmatrix (`compcode`, `tranno`, `remarks`, `label`, `deffective`, `ddue`, `status`, `ddate`, `approved`, `cancelled`) 
                                        VALUES('$company', '$code', '$description', '$label', '$effect', '$due', 'ACTIVE', NOW(), 0, 0)";
                $query = mysqli_query($con, $sql);
            }
            
            $sql = "INSERT INTO discountmatrix_t (`compcode`, `tranno`, `itemno`, `unit`, `discount`, `type`)
                                        VALUES('$company', '$code', '{$data[0]}', '{$data[1]}', '{$data[2]}', '{$data[3]}')";
            if(mysqli_query($con, $sql)){
                $isFinished = true;
            } else {
                $isFinished = false;
            }
                
            
        }

        if($isFinished) { 
            echo json_encode([
                "valid" => true,
                "msg" => "Successfully inserted"
            ]);
        } else { 
            deleteInserted($excel_data);
            echo json_encode([
                "valid" => false,
                "msg" => "Inserting Transaction Failed"
            ]);
        }
    } else { 
        echo json_encode([
            "valid" => false,
            "msg" => "File not found! or File did not match the recommended File Template"
        ]);
    } 

    function deleteInserted($tranno){
        global $con;
        global $company;
        global $code;
        $month = date('m');
        
        $sql = "DELETE FROM discountmatrix WHERE compcode = '$company' AND tranno = '$code' AND MONTH(ddate) = '$month'";
        mysqli_query($con, $sql);
        $sql = "DELETE FROM discountmatrix_t WHERE compcode = '$company' AND tranno = '$code'";
        mysqli_query($con, $sql);
    }