<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    include "../../../Connection/connection_string.php";
    
    $company = $_SESSION['companyid'];
    $transaction = $_REQUEST['transaction'];
    $receipt = [];
    $gl = [];

    $sql = "SELECT a.*, b.dcutdate, b.ddate, b.lapproved, b.lvoid, b.lcancelled, c.creference, d.cname, d.ctin, d.chouseno, d.ccity, d.cvattype FROM receipt_sales_t a
            LEFT JOIN receipt b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
            LEFT JOIN sales_t c ON a.compcode = c.compcode AND a.csalesno = c.ctranno
            LEFT JOIN customers d ON a.compcode = d.compcode AND b.ccode = d.cempid
            WHERE a.compcode = '$company' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND a.ctranno = '$transaction'";

    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) :

        $json = [
            'due' => date("F d, Y", strtotime($list['dcutdate'])),
            'date' => date("F d, Y", strtotime($list['ddate'])),
            'invoice' => $list['csalesno'],
            'reference' => $list['creference'],
            'customer' => $list['cname'],
            'tin' => $list['ctin'],
            'address' => $list['chouseno'] . " " . $list['ccity'],
        ];
        array_push($receipt, $json);
    endwhile;

    $sql = "SELECT a.* FROM glactivity a WHERE a.compcode = '$company' AND a.ctranno = '$transaction'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) :
        array_push($gl, $list);
    endwhile;

    if (!empty($receipt)) {
        echo json_encode([
            'valid' => true,
            'data' => $receipt,
            'GLData' => $gl
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }