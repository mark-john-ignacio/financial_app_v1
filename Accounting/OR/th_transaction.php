<?php
    if(!isset($_SESSION)){
        session_start();
    }
    
    
    include('../../Connection/connection_string.php');
    include('../../include/denied.php');
    
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT a.*, b.cname,b.chouseno,b.ccity,b.cstate,b.ctin,b.cname FROM receipt a 
        left join customers b on a.compcode = b.compcode and a.ccode = b.cempid
        WHERE a.compcode = '$company' and a.ctranno = '$tranno'";

    $query = mysqli_query($con, $sql);
    $data = [];
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $data = $row;
    }

    $sql = "SELECT a.* FROM receipt_sales_t a
    WHERE a.compcode = '$company' and a.ctranno = '$tranno'";
    $query = mysqli_query($con, $sql);
    $data2 = [];
    while($row = $query -> fetch_assoc()){
        array_push($data2, $row);
    }

    echo json_encode([
        'valid' => true,
        'data' => $data,
        'data2' => $data2
    ])
?>