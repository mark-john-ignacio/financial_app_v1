<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    
    include "../../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $datefrom = date("Y-m-d", strtotime($_REQUEST['from']));
    $dateto = date("Y-m-d", strtotime($_REQUEST['to']));
    $other = $_REQUEST['other'];
    $service = $_REQUEST['service'];
    $capital = $_REQUEST['capital'];
    $paybill = [];

    $CAPITALS = [];
    $SERVICES = [];
    $OTHERS = [];

    $sql = "SELECT a.ctranno, a.dapvdate, a.capvno, a.crefrr, a.namount, d.cname, d.ctin, d.chouseno, d.ccity FROM paybill_t a
    LEFT JOIN paybill b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
    LEFT JOIN suppliers d ON a.compcode = d.compcode AND b.ccode = d.ccode
    WHERE a.compcode = '$company' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND (STR_TO_DATE(a.dapvdate, '%Y-%m-%d') BETWEEN '$datefrom' AND '$dateto')";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) > 0) {
        while($list = $query -> fetch_assoc()) :
            $json = [
                'transaction' => $list['ctranno'],
                'date' => date("F d, Y", strtotime($list['dapvdate'])),
                'invoice' => $list['capvno'],
                'reference' => $list['crefrr'],
                'partner' => $list['cname'],
                'tin' => $list['ctin'],
                'address' => $list['chouseno'] . " " . $list['ccity'],
                'gross' => round($list['namount'], 2),
                'net' => !empty($list['nnet']) ? round($list['nnet'], 2) : 0,
                'tax' => !empty($list['nvat']) ? round($list['nvat'], 2) : 0
            ];

            
            if(!in_array($json, $paybill) && $list['ctin'] != ".."){
                array_push($paybill, $json);
            }
        endwhile;
    
        echo json_encode([
            'valid' => true,
            'data' => $paybill
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }