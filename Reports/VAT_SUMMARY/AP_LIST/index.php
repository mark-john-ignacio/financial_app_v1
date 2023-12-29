<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../../../Connection/connection_string.php";
    
    $company = $_SESSION['companyid'];
    $transaction = $_REQUEST['transaction'];
    $details = [];
    $GLAcivity = [];

    $sql = "SELECT a.*, b.ddate, b.dapvdate, b.lapproved, b.lcancelled, b.lvoid, c.cname, c.chouseno, c.ccity, c.ctin FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = b.compcode AND b.ccode = c.ccode
        WHERE a.compcode = '$company' AND a.ctranno = '$transaction'";

    $query = mysqli_query($con, $sql);

    while($list = $query -> fetch_assoc()) :
        $json = [
            'transaction' => $list['ctranno'],
            'date' => date("F d, Y", strtotime($list['ddate'])),
            'due' => date("F d, Y", strtotime($list['dapvdate'])),
            'customer' => $list['cname'],
            'address' => $list['chouseno'] . " " . $list['ccity'],
            'tin' => $list['ctin']
        ];
        
        $cancel = floatval($list['lcancelled']);
        $void = floatval($list['lvoid']);
        $approved = floatval($list['lapproved']);
        array_push($details, $json);
    endwhile;

    $sql = "SELECT a.* FROM glactivity a WHERE a.compcode = '$company' AND a.ctranno = '$transaction'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) :
        array_push($GLAcivity, $list);
    endwhile;

    if(!empty($GLAcivity) && !empty($details)) {
        echo json_encode([
            'valid' => true,
            'data' => $details,
            'GLData' => $GLAcivity,
            'approved' => $approved,
            'void' => $void,
            'cancel' => $cancel
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }