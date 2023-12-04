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

        $balance = $GROSS_BOOK - $GROSS_IMPORT;

        // $pay = $list['pay'];
        $month = date('m');
        $year = date('y');
        $code = '';
        
        // $sql = "SELECT * FROM ";
        $sql = "SELECT * FROM paycheck WHERE compcode = '$company' AND YEAR(`date`) = YEAR(CURDATE()) ORDER BY `tranno` DESC LIMIT 1";
        $query = mysqli_query($con, $sql);

        if (mysqli_num_rows($query) == 0) {
            $code = "BR" . $month . $year . "00000";
        } else {
            $row = $query->fetch_assoc();
            $last = $row['tranno'];

            if (substr($last, 3, 2) !== $month) {
                $code = "BR" . $month . $year . "00000";
            } else {
                $baseno = intval(substr($last, 6, 5)) + 1;
                $code = "BR" . $month . $year . $baseno;
            }
        }

        // $sql = "INSERT INTO paycheck(`compcode`, `module`, `tranno`, `refno`, `debit`, `credit`, `balance`, `bank`, `date`) VALUES ('$company', '$module', '$code'  , '$refno', '$debit', '$credit', '$balance', `bank`, NOW())",
        // if(!mysqli_query($con, $sql)){
        //     $isProceed = false;
        //     break; 
        // } 
    }

    if ($isProceed) {
        echo json_encode([
            'valid' => true,
            'msg' => "Success Fully Matched"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Error occurred while processing transactions"
        ]);
    }
