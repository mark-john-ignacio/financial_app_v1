<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    
    include "../../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $datefrom = date("Y-m-d", strtotime($_REQUEST['from']));
    $dateto = date("Y-m-d", strtotime($_REQUEST['to']));
    $paybill = [];

    $sql = "SELECT a.ctranno, a.dapvdate, a.crefrr, a.namount,  d.cname, d.ctin, d.chouseno, d.ccity,  FROM paybill_t a
    LEFT JOIN paybill b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
    LEFT JOIN suppliers d ON a.compcode = d.compcode AND b.ccode = d.ccode
    WHERE a.compcode = '$company' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND (STR_TO_DATE(a.dapvdate, '%Y-%m-%d') BETWEEN '$datefrom' AND '$dateto')";

    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) :
        if(!in_array($list, $paybill)){
            array_push($paybill, $list);
        }
    endwhile;

    echo json_encode([
        'valid' => true,
        'data' => $paybill
    ]);