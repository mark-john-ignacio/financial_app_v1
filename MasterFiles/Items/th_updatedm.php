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
    $effect = date("Y-m-d", strtotime($_REQUEST['effective']));
    $due = date("Y-m-d", strtotime($_REQUEST['due']));
    $tranno = $_REQUEST['tranno'];


    $sql = "UPDATE discountmatrix SET `remarks` = '$remarks', `label` = '$label', `deffective` = '$effect', `ddue` = '$due' WHERE compcode = '$company' AND tranno = '$tranno'";
    if(mysqli_query($con, $sql)){
        mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
        values('$company','$tranno','$preparedby',NOW(),'UPDATED','DISCOUNT_MATRIX','$compname','Updated Record')");
        
        mysqli_query($con, "UPDATE discountmatrix_t set compcode='xxx' WHERE compcode = '$company' AND tranno = '$tranno'");

        echo json_encode([
            'valid' => true,
            'tranno' => $tranno,
            'msg' => "Successfully Updated"
        ]);
    }
    