<?php

    if(!isset($_SESSION)){
        session_start();
    }
    
    include('../../../Connection/connection_string.php');

    $ctranno = mysqli_real_escape_string($con, $_POST['ctranno']);
    $company = $_SESSION['companyid'];

    $sql = "SELECT a.ncredit, a.ndebit, a.acctno, b.cacctdesc as ctitle from glactivity a 
        left join accounts b on a.compcode=b.compcode and a.acctno=b.cacctid
        where a.compcode = '$company' and a.ctranno = '$ctranno' order by nidentity";
    $result = mysqli_query($con, $sql);
    $data = [];
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        array_push($data, $row);
    }
    echo json_encode([
        'valid' => true,
        'data' => $data
    ])
?>
