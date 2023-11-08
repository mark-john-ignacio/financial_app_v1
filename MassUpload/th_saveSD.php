<?php 

    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $proceed = true;
    $duplicate = false;
    $isFinished = false;

    $chkSales = mysqli_query($con,"select * from discounts where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "DC".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "DC".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "DC".$dmonth.$dyear.$baseno;
		}
	}

    $excel_data = [];
    if (isset($_FILES['excel_file']) || !empty($_FILES['excel_file'])) {
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
                        // echo json_encode($rowdata);
                        array_push($excel_data, $rowdata);
                    }
                }

                unlink($uploadedFile);
            } else {
                echo "Please upload a valid Excel file (XLSX or XLS format).";
                $proceed = false;
            }
        } else {
            echo "Error uploading the file. Please try again.";
            $proceed = false;
        }
    } 

    if($proceed){

        for($i = 1; $i < sizeof($excel_data); $i++){
            $data = $excel_data[$i];
            $sql = "SELECT * FROM `discounts` WHERE `compcode` = '$company' AND `ctranno` = '{$data[0]}'";
            $query = mysqli_query($con, $sql);
            if(mysqli_num_rows($query) != 0){
                $isFinished = false;
            } else {
                $sql = "INSERT INTO discounts (`compcode`, `ctranno`, `clabel`, `cdescription`, `nvalue`, `type`, `ddate`, `deffectdate`, `status`,)
                                        VALUES('$company', '$cSINo', '{$data[0]}', '{$data[1]}', '{$data[2]}', '{$data[3]}', NOW(), '{$data[4]}', 'ACTIVE')";

                if(mysqli_query($con, $sql)){
                    $isFinished = true;
                } else {
                    $isFinished = false;
                }
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
        global $cSINo;
        $month = date('m');
        
        for($i = 1; $i < sizeof($tranno); $i++){
            $sql = "DELETE FROM discounts WHERE compcode = '$company' AND ctranno = '$cSINo' AND MONTH(ddate) = '$month'";
            mysqli_query($con, $sql);
        }
    }