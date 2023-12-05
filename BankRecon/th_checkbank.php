<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    include "../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $details = json_decode($_REQUEST["details"], true);
    $bank = $_REQUEST['bank'];
    $isProceed = true;
    
    foreach ($details as $list) {
        $tranno = $list['tranno'];
        $module = $list['module'];
        $refno = $list['refno'];
        $credit = $list['credit'];
        $debit = $list['debit'];

        $book_credit = floatval($list['book_credit']);
        $book_debit = floatval($list['book_debit']);

        $GROSS_BOOK = floatval($book_credit) + floatval($book_debit);
        $GROSS_IMPORT = floatval($debit) - floatval($credit);


        // $pay = $list['pay'];
        $month = date('m');
        $year = date('y');
        $code = '';

        // $sql = "INSERT INTO paycheck(`compcode`, `module`, `tranno`, `refno`, `debit`, `credit`, `bank`, `date`) VALUES ('$company', '$module', '$tranno', '$refno', '$debit', '$credit', '$bank', NOW())";
        // if(!mysqli_query($con, $sql)){
        //     $isProceed = false;
        //     break; 
        // } 
    }

    if ($isProceed) {
        echo json_encode([
            'valid' => true,
            'msg' => "Success Fully Matched",
            'data' => $details
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Error occurred while processing transactions"
        ]);
    }
