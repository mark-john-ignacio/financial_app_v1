<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    include "../Model/helper.php";
    $company = $_SESSION['companyid'];
    $refno = $_REQUEST['refno'];
    $excel = ExcelRead($_FILES);

    $sql = "SELECT * FROM deposit WHERE compcode = '$company' AND creference='$refno'";
    $query = mysqli_query($con, $sql);

    $deposit = [];
    while($row = $query -> fetch_assoc()){
        array_push($deposit, $row); 
    }

    echo json_encode([
        'valid' => true,
        'deposit' => $deposit
    ]);