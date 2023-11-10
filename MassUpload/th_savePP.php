<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    include("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $proceed = true;
    $duplicate = false;
    $isFinished = false;

    $supplier = $_POST['supplier'];
    $remarks = $_POST['description'];
    $effectivityDate = $_POST['effectdate'];

    $dmonth = date('m');
    $dyear = date('y');

    $result = mysqli_query ($con, "SELECT * FROM items_purch_cost where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1"); 
    if(mysqli_num_rows($result)==0){
		$code = "PP".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dmonth){
			$code = "PP".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$code = "PP".$dmonth.$dyear.$baseno;
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
                        $cellValue = $cell->getValue();

                        if (!is_null($cellValue) and !empty($cellValue)) {
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
    
    if($proceed){
        for($i = 0; $i < sizeof($excel_data); $i++){
            $data = $excel_data[$i];
            if($i === 0){
                $sql = "INSERT INTO items_purch_cost (compcode, ctranno, ccode, ddate, deffectdate, cremarks, lapproved, lcancelled, lposted)
                                            VALUES('$company', '$code', '$supplier', NOW(), '$effectivityDate', '$remarks', 0,0,0)";
                if(!mysqli_query($con, $sql)){
                    $isFinished = false;
                    break;
                } 
            } else {
                $identity = $code. "P".$i;
                $item = $data[0];
                $unit = $data[1];
                $price = $data[2];
                $sql = "SELECT * FROM items WHERE compcode = '$company' AND cpartno = '$item' AND cunit = '$unit'";
                $query = mysqli_query($con, $sql);
                if(mysqli_num_rows($query) != 0){
                    $sql = "INSERT INTO items_purch_cost_t (compcode, cidentity, nident, ctranno, citemno, cunit, nprice)
                                                VALUES('$company', '$identity', '$i', '$code', '$item', '$unit', '$price' )";
                    if(mysqli_query($con, $sql)){
                        $isFinished = true;
                    } else {
                        $isFinished = false;
                        break;
                    }
                }else {
                    $isFinished = false;
                    break;
                }
                
            }
        }

        if($isFinished){
            echo json_encode([
                'valid' => true,
                'msg' => "Mass Upload Complete"
            ]);
        } else {
            deleteInserted($code);
            echo json_encode([
                'valid' => true,
                'msg' => "Mass Upload Failed"
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
        $month = date('m');
        
        for($i = 1; $i < sizeof($tranno); $i++) {
            $sql = "DELETE FROM items_purch_cost WHERE compcode = '$company' AND ctranno = '$tranno' AND MONTH(ddate) = '$month'";
            mysqli_query($con, $sql);
            $sql = "DELETE FROM items_purch_cost_t WHERE compcode = '$company' AND ctranno = '$tranno'";
            mysqli_query($con, $sql);
        }
    }