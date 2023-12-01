<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    $tranno = $_REQUEST['tranno'];
    $module = $_REQUEST['module'];
    $refno = $_REQUEST['refno'];
    $gross = $_REQUEST['gross'];
    $pay = $_REQUEST['pay'];

    $sql = "SELECT * FROM paycheck WHERE compcode = '$company' AND ctranno='$tranno' AND YEAR(`date`) = YEAR(CURDATE()) ORDER BY `tranno` DESC LIMIT 1";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query)==0) {
        $code = "BR".$month.$year."00000";
    } else {
        while($row = $query -> fetch_assoc()){
            $last = $row['tranno'];
        }
        
        if(substr($last,3,2) <> $month){
            $code = "BR".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,6,5)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $code = "BR".$month.$year.$baseno;
        }
    }
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0 ){
        echo json_encode([
            'valid' => false,
            'msg' => "This transaction has been already filed"
        ]);
    } else {
        $sql = match($module){
            "PV" => "INSERT INTO paycheck(`compcode`, `module`, `tranno`, `refno`, `debit`, `credit`, `balance`, `date`) VALEUS ('$company', '$module', '$code', '$refno', '$gross', 0, '$gross', NOW())",
            "OR" => "INSERT INTO paycheck(`compcode`, `module`, `tranno`, `refno`, `debit`, `credit`, `balance`, `date`) VALEUS ('$company', '$module', '$code', '$refno', 0, '$gross', '$gross', NOW())",
        };
    }