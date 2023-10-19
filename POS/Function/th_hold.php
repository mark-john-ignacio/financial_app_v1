<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    // include('../../include/denied.php');
    // include('../../include/access2.php');

    $company = $_SESSION['companyid'];

    $transaction = $_REQUEST['code'];
    $remarks = $_REQUEST['remarks'];
    $dates = date('Y-m-d h:i:s');
    


    $sql = "INSERT INTO pos_hold 
    (`compcode`, `transaction`, `remarks`, `trandate`) 
    VALUES ('$company', '$transaction', '$remarks', '$dates')";

    if(mysqli_query($con, $sql)){
        echo json_encode([
            'valid' => true,
            'msg' => "Success"
        ]);
    }