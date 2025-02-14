<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../Connection/connection_string.php");
    require_once("../Model/helper.php");
    $company = $_SESSION['companyid'];
    $proceed = true;
    $duplicate = false;
    $isFinished = false;

    $excel_data = ExcelRead($_FILES);

    $xxcmsg = "";

    $okinsert = 0;
    if(count($excel_data) != 0){

        for($i = 1; $i < sizeof($excel_data); $i++){
            $data = $excel_data[$i];
            $sql = "SELECT * FROM `coupon` WHERE `compcode` = '$company' AND `CouponNo` = '{$data[0]}'";
            $query = mysqli_query($con, $sql);
            if(mysqli_num_rows($query) != 0){
                $isFinished = false;
            } else {
                $sql = "INSERT INTO `coupon`(`compcode`, `CouponNo`, `label`, `remarks`, `barcode`, `price`, `days`, `status`, `ddate`, `cacctcode`) VALUES ('$company', '{$data[0]}', '{$data[1]}', '{$data[2]}', '{$data[3]}', '{$data[4]}', '{$data[5]}', 'INACTIVE', NOW(), '{$data[6]}')";
                
                if(!mysqli_query($con, $sql)){
                //    $isFinished = false;
                }else{
                    $okinsert++;
                }
            }
            
        }    

        $totexcel = count($excel_data)-1;
        if($okinsert>0){
            if($okinsert<$totexcel){
                $y1 = "Some Data are inserted successfully";
                echo json_encode([
                    "valid" => true,
                    "msg" => $y1
                ]);
            }
            if($okinsert==$totexcel){
                echo json_encode([
                    "valid" => true,
                    "msg" => "All Data Successfully inserted"
                ]);
            }
        }
        else { 
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

    function deleteInserted($coupon){
        global $con;
        global $company;
        $month = date('m');
        
        for($i = 1; $i < sizeof($coupon); $i++){
            $sql = "DELETE FROM coupon WHERE compcode = '$company' AND CouponNo = '{$coupon[$i][0]}' AND MONTH(ddate) = '$month'";
            mysqli_query($con, $sql);
        }
    }