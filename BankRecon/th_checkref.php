<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    include "../Model/helper.php";

    function nullstring($data){
        return $data != "" || empty($data);
    }

    $company = $_SESSION['companyid'];
    $refno = $_POST['refno'];
    $date = date("Y-m-d", strtotime($_POST['date']));
    $name = $_POST['name'];
    $bank = $_POST['bank'];
    $excel = ExcelRead($_FILES);

    // $sql = "SELECT * FROM deposit WHERE compcode = '$company' AND creference='$refno' AND STR_TO_DATE(dcutdate, '%Y-%m-%d') = '$date'";
    

    $sql = "SELECT * FROM bank WHERE compcode = '$company' AND cacctno = '$bank'";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_array(MYSQLI_ASSOC);
    $bankcode = $row['ccode'];
    

    $isReference = (nullstring($refno)) ? "AND a.acctno = '$bank'" : "";
    $isBillRef = (nullstring($refno)) ? "AND a.cpayrefno = '$refno'" : "";
    $isCheck = (nullstring($refno)) ? "AND ccheckno = '$refno'" : "";


    $sql = "SELECT a.* FROM glactivity a WHERE a.compcode = '$company' ". $isReference ." AND STR_TO_DATE(a.ddate, '%Y-%m-%d') = '$date'";
    $query = mysqli_query($con, $sql);

    $deposit = [];
    while($row = $query -> fetch_assoc()){
        // array_push($deposit, $row); 
        $tranno = $row['ctranno'];
        $module = $row['cmodule'];
        $sql = match($row['cmodule']){
            "PV" => "SELECT SUM(npaid) as paid FROM paybill WHERE compcode = '$company' AND ctranno = '$tranno' " . $isBillRef . " AND cbankcode = '$bankcode' AND STR_TO_DATE(a.dcheckdate, '%Y-%m-%d') = '$date'",
            "OR" => "SELECT SUM(nchkamt) as paid FROM receipt_check_t WHERE compcode ='$company' AND ctranno = '$tranno' AND cbank ='$bank' " . $isReceiptRef . " AND (a.ddate, '%Y-%m-%d') = '$date'"
        };
        $query = mysqli_query($con, $sql);
        $row = $query -> fetch_assoc();
        $json = [
            'paid' => $row['paid'],
            'module' => $module,
            'valid' => true
        ];
        array_push($deposit, $json);

    }

    

    if(!empty($deposit)){
        echo json_encode([
            'valid' => true,
            'deposit' => $deposit,
            'msg' => "Cheque Account matches"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Cheque Account has no match",
            'data' => $_POST
        ]);
    }