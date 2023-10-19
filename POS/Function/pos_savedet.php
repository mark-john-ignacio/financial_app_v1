<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    /**
     * Initiate Variables
     */
    $tranno = mysqli_real_escape_string($con, $_POST['tranno']);
    $item = mysqli_real_escape_string($con, $_POST['itm']);
    $unit = mysqli_real_escape_string($con, $_POST['unit']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);
    $amount = mysqli_real_escape_string($con, $_POST['amount']);
    // $net = mysqli_real_escape_string($con, $_POST['net']);
    // $vat = mysqli_real_escape_string($con, $_POST['vat']);
    // $gross = mysqli_real_escape_string($con, $_POST['gross']);

    $net =  number_format($amount, 2) / number_format(1 + (12/100),2);
    $vat = $net * (12/100);
    $price = $amount / $quantity;

    $sql = "INSERT INTO pos_t (`compcode`, `tranno`, `item`, `uom`, `quantity`, `amount`, `net`, `vat`, `gross`) 
        VALUES('$company', '$tranno', '$item', '$unit', '$quantity', '$price', '$net', '$vat', '$amount')";
    
    if(mysqli_query($con, $sql)){
        echo json_encode([
            'valid' => true,
            'msg' => "Successfully Inserted"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Unsuccessfully Inserted"
        ]);
    }