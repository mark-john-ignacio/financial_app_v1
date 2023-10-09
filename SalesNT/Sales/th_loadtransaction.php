<?php 
    if(!isset($_SESSION)){
    session_start();
    }
    require_once "../../Connection/connection_string.php";
    
    $company = $_SESSION['companyid'];
    $date1 = date("Y-m-d");
    $csalesno = $_REQUEST['tranno'];

    $sqlbody = mysqli_query($con,"select a.*, d.ngross, b.citemdesc, c.nrate from ntsales_t a 
    left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
    left join taxcode c on a.compcode=c.compcode and a.ctaxcode=c.ctaxcode 
    left join sales d on a.compcode = d.compcode and a.ctranno = d.ctranno
    where a.compcode='$company' and a.ctranno = '$csalesno'");
    $data =[];
    while($row = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
        array_push($data, $row);
    }

    echo json_encode([
        'valid' => true,
        'data' => $data
    ]);