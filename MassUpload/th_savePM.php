<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include("../Connection/connection_string.php");
    require_once("../Model/helper.php");
    $company = $_SESSION['companyid'];
    $duplicate = false;
    $isFinished = false;

    $matrix = $_POST['matrix'];
    $remarks = $_POST['description'];
    $effectivityDate = date("Y-m-d", strtotime($_POST['effectdate']));

    $today = date("mdy");
    $code = $matrix . $today;
    $result = mysqli_query ($con, "SELECT ctranno, IF(LOCATE('_', ctranno), SUBSTRING_INDEX(ctranno,'_',-1), '1') as prefx FROM items_pm where compcode='$company' and ctranno like '$code%' order by ctranno DESC LIMIT 1"); 
	
	
	//echo $row['prefx'];
	
	if(mysqli_num_rows($result)==0){
		$code = $code."_1";
	}
	else {
		$row = mysqli_fetch_assoc($result);
		$yz = $row['prefx'];
		
		$prfx = (int)$yz+1;
		
		$code = $code."_".$prfx;
		
		//echo $code;
	}

    $excel_data = ExcelRead($_FILES);

    if(count($excel_data) != 0){
        for($i = 0; $i < sizeof($excel_data); $i++){
            $data = $excel_data[$i];
            if($i === 0){
                $sql = "INSERT INTO items_pm (compcode, ctranno, cbatchno, cversion, cremarks, ddate, deffectdate, lapproved, lcancelled, lprintposted)
                                    VALUES('$company', '$code', '$code', '$matrix', '$remarks', NOW(), '$effectivityDate', 0, 0, 0)";
                if(!mysqli_query($con, $sql)){
                    $isFinished = false;
                    break;
                }
            } else {
                $identity = $code. "P".$i;
                $sql = "SELECT * FROM items WHERE compcode = '$company' AND cpartno = '{$data[0]}' AND cunit = '{$data[1]}'";
                $query = mysqli_query($con, $sql);
                if(mysqli_num_rows($query) != 0){
                    $sql = "INSERT INTO items_pm_t (compcode, cidentity, nident, ctranno, citemno, cunit, nprice)
                                            VALUES('$company', '$identity', '$i', '$code', '{$data[0]}', '{$data[1]}', '{$data[2]}')";
                    if(mysqli_query($con, $sql)){
                        $isFinished = true;
                    } else {
                        $isFinished = false;
                        break;
                    }   
                } else {
                    $isFinished = false;
                }
            }
        }

        if($isFinished){
            echo json_encode([
                'valid' => true,
                'msg' => "Successfully Inserted"
            ]);
        } else {
            deleteInserted($code);

            echo json_encode([
                'valiid' => false,
                'msg' => "Data has been failed to insert!"
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
            $sql = "DELETE FROM items_pm WHERE compcode = '$company' AND ctranno = '$tranno' AND MONTH(ddate) = '$month'";
            mysqli_query($con, $sql);
            $sql = "DELETE FROM items_pm_t WHERE compcode = '$company' AND ctranno = '$tranno'";
            mysqli_query($con, $sql);
        }
    }