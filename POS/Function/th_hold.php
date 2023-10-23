<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    // include('../../include/denied.php');
    // include('../../include/access2.php');

    $company = $_SESSION['companyid'];

    $transaction = $_REQUEST['code'];
    $table = $_REQUEST['table'];
    $orderType = $_REQUEST['order'];
    $dates = date('Y-m-d h:i:s');
    


    $sql = "INSERT INTO pos_hold (`compcode`, `transaction`, `table`, `ordertype` , `trandate`) 
    VALUES ('$company', '$transaction', '$table', '$orderType', '$dates')";

    if(mysqli_query($con, $sql)){
        echo json_encode([
            'valid' => true,
            'msg' => "Success"
        ]);
    }