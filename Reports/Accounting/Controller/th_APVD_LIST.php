<?php

    if(!isset($_SESSION)){
        session_start();
    }
    
    include('../../../Connection/connection_string.php');

    $ctranno = mysqli_real_escape_string($con, $_POST['ctranno']);
    $company = $_SESSION['companyid'];

    $sql = "select b.* from apv_d a
    left join apv_t b on a.compcode = b.compcode and a.ctranno = b.ctranno
    left join apv c on a.compcode = c.compcode and a.ctranno = c.ctranno
    where a.compcode = '$company' and a.ctranno = '$ctranno'";

    
    $result = mysqli_query($con, $sql);
    // $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $data = [];
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        array_push($data, $row);
    }

    echo json_encode([
            'valid'=> true,
            'data'=> $data
        ]);