<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include('../../Connection/connection_string.php');
    // include('../../include/denied.php');
    // include('../../include/access2.php');

    $company = $_SESSION['companyid'];

    $table = $_REQUEST['table'] != null ? $_REQUEST['table'] : null;
    $orderType = $_REQUEST['type'] != null ? $_REQUEST['type'] : null;
    $dates = date('Y-m-d h:i:s');
    
    $month = date('m');
    $year = date('y');
    $transaction ='';

    $sql = "SELECT * FROM pos_hold  where compcode='$company' and YEAR(trandate) = YEAR(CURDATE()) Order By `transaction` desc LIMIT 1";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query)==0) {
        $transaction = "POS".$month.$year."00000";
    }
    else {
        while($row = $query -> fetch_assoc()){
            $last = $row['transaction'];
        }
        
        
        if(substr($last,3,2) <> $month){
            $transaction = "POS".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,7,6)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $transaction = "POS".$month.$year.$baseno;
        }
    }

    $sql = "INSERT INTO pos_hold (`compcode`, `transaction`, `table`, `ordertype` , `trandate`) 
    VALUES ('$company', '$transaction', '$table', '$orderType', '$dates')";

    if(mysqli_query($con, $sql)){
        echo json_encode([
            'valid' => true,
            'msg' => "Success",
            'tranno' => $transaction
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Failed"
        ]);
    }