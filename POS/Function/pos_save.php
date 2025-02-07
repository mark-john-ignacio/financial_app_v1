<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    
    $month = date('m');
    $year = date('y');

    $sql = "SELECT * FROM pos  where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By `tranno` desc LIMIT 1";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query)==0) {
        $code = "POS".$month.$year."00000";
    } else {
        while($row = $query -> fetch_assoc()){
            $last = $row['tranno'];
        }
        
        
        if(substr($last,3,2) <> $month){
            $code = "POS".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,7,6)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $code = "POS".$month.$year.$baseno;
        }
    }

    /**
     * Initiate all variables
     */
    $prepared = mysqli_real_escape_string($con, $_SESSION['employeename']);
    $date = date('Y-m-d h:i:s');
    $amount = mysqli_real_escape_string($con, $_POST['amount']);
    $net = mysqli_real_escape_string($con, $_POST['net']);
    $vat = mysqli_real_escape_string($con, $_POST['vat']);
    $gross = mysqli_real_escape_string($con, $_POST['gross']);
    $discount = mysqli_real_escape_string($con, $_POST['discount']);
    $tendered = mysqli_real_escape_string($con, $_POST['tendered']);
    $exchange = mysqli_real_escape_string($con, $_POST['exchange']);
    $subtotal = mysqli_real_escape_string($con, $_POST['subtotal']);

    $customer = mysqli_real_escape_string($con, ($_REQUEST['customer'] != "") ? $_REQUEST['customer'] : "WALK-IN");
    $type = mysqli_real_escape_string($con, $_POST['order']);
    $table = mysqli_real_escape_string($con, $_POST['table']);
    $coupon = mysqli_real_escape_string($con, $_POST['coupon']);
    $tranno = mysqli_real_escape_string($con, $_POST['tranno']);
    $service = mysqli_real_escape_string($con, $_POST['service']);

    $pay_method = mysqli_real_escape_string($con, $_POST['method']);
    $reference = mysqli_real_escape_string($con, $_POST['reference']);
    
    $h_tranno = mysqli_real_escape_string($con, $_POST['holdtranno']);

    $customerName = mysqli_real_escape_string($con, $_POST['customerName']);
    $customerAddress = mysqli_real_escape_string($con, $_POST['customerAddress']);
    $customerTIN     = mysqli_real_escape_string($con, $_POST['customerTin']);


    if(!empty($tranno)){
        mysqli_query($con, "DELETE FROM pos_hold WHERE `compcode` = '$company' AND `transaction` = '$tranno'");
        mysqli_query($con, "DELETE FROM pos_hold_t WHERE `compcode` = '$company' AND `transaction` = '$tranno'");
    }

    
    /**
     * Query for Inserting into database
     */
    $sql = "INSERT INTO pos (`compcode`, `tranno`, `preparedby`, `ddate`, `amount`, `net`, `vat`, `gross`, `discount`, `tendered`, `exchange`, `customer`, `orderType`, `table`, `coupon`, `serviceFee`, `subtotal`, `payment_method`, `payment_reference`, `cust_name`, `cust_address`, `cust_tin`)
            VALUES ('$company', '$code', '$prepared', '$date', '$amount', '$net', '$vat', '$gross', '$discount', '$tendered', '$exchange', '$customer', '$type', '$table', '$coupon', '$service', '$subtotal', '$pay_method', '$reference', '$customerName', '$customerAddress', '$customerTIN')";

    if(mysqli_query($con, $sql)){

        $sql_select_pending = "SELECT * FROM pendingorder_status WHERE tranno = '$tranno'";
        $result_pending = mysqli_query($con, $sql_select_pending);

        if(mysqli_num_rows($result_pending) > 0){
            $sql = "UPDATE pendingorder_status SET payment_transaction = '$code', transaction_type = 'Payment', tranno = '$code' WHERE tranno = '$tranno' AND transaction_type = 'Hold'";
            mysqli_query($con, $sql);

            echo json_encode([
                'valid' => true,
                'tranno' => $code,
                'flag_update' => "Update",
                'msg' => "Payment Successfully added"
            ]);

            
        }
        else{
            echo json_encode([
                'valid' => true,
                'tranno' => $code,
                'flag_update' => "Insert",
                'msg' => "Payment Successfully added"
            ]);
        }
    } else {
        $compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        $sql = "INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `module`, `cevent`, `cmachine`, `cremarks`)
            VALUES ('$company', '$code',  '$prepared', NOW(), 'POS', 'INSERTED', '$compname', 'Inserted New Record')";
        mysqli_query($con, $sql);
        // Delete previous details
        mysqli_query($con, "Delete from pos_t WHERE compcode='$company' and tranno='$code'");
        
        echo json_encode([
            'valid' => false,
            'msg' => "Unsuccessful Inserted"
        ]);
    }