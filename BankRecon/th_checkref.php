<?php

    if(!isset($_SESSION)){
        session_start();
    }
    include "../Connection/connection_string.php";
    include "../Model/helper.php";

    function nullstring($data){
        return $data != "" && $data != null && !empty($data);
    }

    $company = $_SESSION['companyid'];
    $refno = $_POST['refno'];
    $date = date("Y-m-d", strtotime($_POST['date']));
    $name = $_POST['name'];
    $bank = $_POST['bank'];
    $transactions = json_decode($_POST['tranno'], true);
    $excel = ExcelRead($_FILES);
    

    $sql = "SELECT * FROM bank WHERE compcode = '$company' AND cacctno = '$bank'";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_array(MYSQLI_ASSOC);
    $bankcode = $row['ccode'];

    $isReference = (nullstring($refno)) ? "AND a.acctno = '$bank'" : "";
    // $isBillRef = (nullstring($refno)) ? "AND cpayrefno = '$refno'" : "";
    // $isCheck = (nullstring($refno)) ? "AND ccheckno = '$refno'" : "";


    $sql = "SELECT a.* FROM glactivity a WHERE a.compcode = '$company' ". $isReference ." AND STR_TO_DATE(a.ddate, '%Y-%m-%d') = '$date'";
    $query = mysqli_query($con, $sql);

    $deposit = [];
    while($row = $query -> fetch_assoc()){
        // array_push($deposit, $row); 
        $tranno = $row['ctranno'];
        $module = $row['cmodule'];
        $credit = $row['ncredit'];
        $debit = $row['ndebit'];

        if(!in_array($tranno, $transactions)){
            $sql = match($module){
                "PV" => "SELECT cpayee as named, cpayrefno as refno FROM paybill WHERE compcode = '$company' AND ctranno = '$tranno' AND cbankcode = '$bankcode' AND STR_TO_DATE(dcheckdate, '%Y-%m-%d') = '$date' AND lapproved = 1 AND lvoid = 0",
                "OR" => "SELECT a.cname as named, a.ccheckno as refno FROM receipt_check_t a
                        LEFT JOIN receipt b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
                        LEFT JOIN customers c ON a.compcode = c.compcode AND b.ccode = c.cempid
                        WHERE a.compcode ='$company' AND a.ctranno = '$tranno' AND a.cbank ='$bank' AND (a.ddate, '%Y-%m-%d') = '$date' AND b.lapproved = 1 AND b.lvoid = 0",
                        
                // "JE" => "SELECT ncredit as credit FROM journal_t WHERE compcode = '$company' AND cacctno = '$bank' and ctranno in (
                //     SELECT ctranno FROM journal WHERE compcode = '$company' AND ctranno = '$tranno' AND lapproved = 1 AND lvoid = 0
                // )"
            };
            $queries = mysqli_query($con, $sql);
            $rows = $queries -> fetch_assoc();
            $json = [
                // 'paid' => $rows['credit'] ? round($rows['credit'],2) : 0,
                'credit' => round($credit,2),
                'debit' => round($debit,2),
                'name' => $rows['named'],
                'refno' => $rows['refno'] ? $rows['refno'] : "",
                'date' => $date,
                'module' => $module,
                'tranno' => $tranno
            ];
            array_push($deposit, $json);
        }
        
    }
    

    if(!empty($deposit)){
        echo json_encode([
            'valid' => true,
            'data' => $deposit,
            'msg' => "Cheque Account matches"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Cheque Account has no match",
            'data' => $_POST,
        ]);
    }