<?php
    if(!isset($_SESSION)){
        session_start();
    }
    
    
    include('../../Connection/connection_string.php');
    include('../../include/denied.php');
    
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];

    $sql = "SELECT a.*, b.cname,b.chouseno,b.ccity,b.cstate,b.ctin,b.cname, c.csalesno FROM receipt a 
        left join customers b on a.compcode = b.compcode and a.ccode = b.cempid
        left join receipt_sales_t c on a.compcode = c.compcode and a.ctranno = c.ctranno
        WHERE a.compcode = '$company' and a.ctranno = '$tranno'";

    $query = mysqli_query($con, $sql);
    $data = [];
    while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
        $data = $row;
    }

    $checksalesno = substr($data['csalesno'], 0, 2);
    $data2 = [];

    $sql = match($checksalesno){
        'SI' => "SELECT a.*, b.ngross, b.csalestype, c.* FROM receipt_sales_t a
                left join sales b on a.compcode = b.compcode and a.csalesno = b.ctranno
                left join sales_t c on a.compcode = c.compcode and a.csalesno = c.ctranno
                WHERE a.compcode = '$company' and a.ctranno = '$tranno'",

        "IN" => "SELECT a.*, b.ngross, b.csalestype, c.* FROM receipt_sales_t a
                left join ntsales b on a.compcode = b.compcode and a.csalesno = b.ctranno
                left join ntsales_t c on a.compcode = c.compcode and a.csalesno = c.ctranno
                WHERE a.compcode = '$company' and a.ctranno = '$tranno'",
        
        default => null
    };

    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($data2, $row);
    }

    echo json_encode([
        'valid' => true,
        'data' => $data,
        'data2' => $data2
    ])
?>