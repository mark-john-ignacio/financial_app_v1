<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    // include('../../include/denied.php');
    // include('../../include/access2.php');

    $company = $_SESSION['companyid'];

    $transaction = $_REQUEST['code'];
    $partno = $_REQUEST['partno'];
    $item = $_REQUEST['name'];
    $unit = $_REQUEST['unit'];
    $quantity =$_REQUEST['quantity'];
    $cost = $_REQUEST['cost'];

    $type = $_REQUEST['type'];
    // $table = $_REQUEST['table'];
    $table = "sample table 1";
    

    
    $sql = "SELECT * FROM pos_hold WHERE `compcode` = '$company' and `transaction` = '$transaction'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) !== 0){

        $sql = "INSERT INTO pos_hold_t 
        (`compcode`, `transaction`, `partno`, `item`, `quantity`, `unit`, `type`, `table`, `discount`, `cost`) 
        VALUES ('$company', '$transaction', '$partno', '$item', '$quantity', '$unit', '$type', '$table', '0', '$cost')";
        $query = mysqli_query($con, $sql);

        echo json_encode([
            'valid' => true,
            'data' => 'sucess'
        ]);

    } else {
        $sql = "DELETE FROM pos_hold WHERE `transaction` = '$transaction'";
        mysqli_query($con, $sql);

        echo json_encode([
            'valid' => false,
            'msg' => "POS Transaction Cannot Hold!"
        ]);
    }