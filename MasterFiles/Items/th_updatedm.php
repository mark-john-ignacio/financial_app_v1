<?php 


    if(!isset($_SESSION)){
        session_start();
    }

    include ("../../Connection/connection_string.php");
    $company = $_SESSION["companyid"];
    $compname = php_uname('n');
    $preparedby = $_SESSION['employeeid'];

    $remarks = $_REQUEST['remarks'];
    $label = $_REQUEST['label'];
    $effective = $_REQUEST['effective'];
    $due = $_REQUEST['due'];
    $tranno = $_REQUEST['tranno'];

    $item = json_decode($_REQUEST['items']);
    $type = json_decode($_REQUEST['type']);
    $discount = json_decode($_REQUEST['discount']);
    
    $isSuccess = false;
    for($i = 0; $i < sizeof($item); $i++){
        $sqlupdate = "UPDATE discountmatrix_t SET `type` ='{$type[$i]}', `discount` = '{$discount[$i]}' WHERE compcode = '$company' AND tranno = '$tranno' AND itemno = '{$item[$i]}'";

        // if(!mysqli_query($con, "SELECT * discountmatrix_t WHERE compcode = '$company' AND tranno = '$tranno' AND itemno = '".$item[$i]."'")){
        //         //insert 
        // }

        if(mysqli_query($con, $sqlupdate)){
            $isSuccess = true;
        } else {
            $isSuccess = false;
            break;
        }
        
    }
    
    if($isSuccess){

        $sql = "UPDATE discountmatrix SET `remarks` = '$remarks', `label` = '$label', `deffective` = '$effective', `ddue` = '$due' WHERE compcode = '$company' AND tranno = '$tranno'";
        if(mysqli_query($con, $sql)){
            mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
            values('$company','$tranno','$preparedby',NOW(),'UPDATED','DISCOUNT_MATRIX','$compname','Updated Record')");
            

            echo json_encode([
                'valid' => true,
                'msg' => "Discount has successfully update!"
            ]);
        }
    } else {
        // mysqli_query($con, "DELETE FROM discountmatrix WHERE compcode = '$company' AND tranno = '$tranno'");
        // mysqli_query($con, "DELETE FROM discountmatrix_t WHERE compcode = '$company' AND tranno = '$tranno'");

        echo json_encode([
            'valid' => false,
            // 'msg' => "Discount has not been update!"
            'msg' => $item
        ]); 
    }