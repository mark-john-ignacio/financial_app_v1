<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    include "../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $employee = $_SESSION['employeeid'];
    $tranno = json_decode($_REQUEST["tranno"], true);
    $module = json_decode($_REQUEST["module"], true);
    $refno = json_decode($_REQUEST["refno"], true);
    $credit = json_decode($_REQUEST["credit"], true);
    $debit = json_decode($_REQUEST["debit"], true);

    $bank = $_REQUEST['bank'];
    $paycheck = [];
    $isProceed = true;
    
    for($i = 0; $i < count($tranno); $i++){
        $TRANNO_VALUE = $tranno[$i];
        $MODULE_VALUE = $module[$i];
        $REFNO_VALUE = $refno[$i];
        $CREDIT_VALUE = $credit[$i];
        $DEBIT_VALUE = $debit[$i];

        $sql ="SELECT * FROM paycheck WHERE compcode= '$company'";
        $query = mysqli_query($con, $sql);
        while($row = $query -> fetch_assoc()){
            $transaction = $row['tranno'];
            array_push($paycheck, $transaction);
        }

        if(!in_array($TRANNO_VALUE, $paycheck)){
            $sql = "INSERT INTO paycheck(`compcode`, `module`, `tranno`, `refno`, `debit`, `credit`, `bank`, `preparedby`, `date`) VALUES ('$company', '$MODULE_VALUE', '$TRANNO_VALUE', '$REFNO_VALUE', '$DEBIT_VALUE', '$CREDIT_VALUE', '$bank', '$employee', NOW())";
            if(!mysqli_query($con, $sql)){
                $isProceed = false;
                break; 
            }
        } 
    }
    
    if ($isProceed) {
        echo json_encode([
            'valid' => true,
            'msg' => "Success Fully Matched",
            'data' => $tranno
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Error occurred while processing transactions"
        ]);
    }
