<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    
    include "../../../Connection/connection_string.php";

    $company = $_SESSION['companyid'];
    $datefrom = date("Y-m-d", strtotime($_REQUEST['from']));
    $dateto = date("Y-m-d", strtotime($_REQUEST['to']));

    $other1 = "VTGIMOCG";
    $other2 = "VTGOCG";
    $service1 = "VTSDOM";
    $service2 = "VTSNR";
    $capital1 = "VTGE1M";
    $capital2 = "VTGNE1M";

    $CAPITALS = [];
    $SERVICES = [];
    $OTHERS = [];

    // $sql = "SELECT a.ctranno, a.dapvdate, a.capvno, a.crefrr, a.namount, d.cname, d.ctin, d.chouseno, d.ccity FROM paybill_t a
    // LEFT JOIN paybill b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
    // LEFT JOIN suppliers d ON a.compcode = d.compcode AND b.ccode = d.ccode
    // WHERE a.compcode = '$company' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND (STR_TO_DATE(a.dapvdate, '%Y-%m-%d') BETWEEN '$datefrom' AND '$dateto')";

    $sql = "SELECT a.ctranno, a.ndebit, a.ncredit, a.ctaxcode, b.ngross, b.dapvdate, c.cname, c.chouseno, c.ccity, c.ctin FROM glactivity a 
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = c.compcode AND b.ccode = c.ccode
        WHERE a.compcode = '$company' AND a.cmodule = 'APV' AND (a.ctaxcode in ('$other1', '$capital1', '$service1', '$other2', '$capital2', '$service2')) AND (STR_TO_DATE(b.ddate, '%Y-%m-%d') BETWEEN '$datefrom' AND '$dateto') AND b.lcancelled = 0 AND b.lvoid = 0;";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) > 0) {
        while($list = $query -> fetch_assoc()) :

            $json = [
                'transaction' => $list['ctranno'],
                'debit' => round($list['ndebit'], 2),
                'credit' => round($list['ncredit'], 2),
                'gross' => round($list['ngross'], 2),
                // 'reference' => $list['crefrr'],
                'date' => $list['dapvdate'],
                'partner' => $list['cname'],
                'address' => $list['chouseno'] . " " . $list['ccity'],
                'tin' => $list['ctin']
            ];
            
            $taxcode = $list['ctaxcode'];
            switch($taxcode) {
                case $other1: 
                case $other2: 
                    array_push($OTHERS, $json);
                    break;
                case $capital1:
                case $capital2:
                    array_push($CAPITALS, $json);
                    break;
                case $service1:
                case $service2:
                    array_push($SERVICES, $json);
                    break;
            }
        endwhile;
    
        echo json_encode([
            'valid' => true,
            'service' => $SERVICES,
            'capital' => $CAPITALS,
            'other' => $OTHERS
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => ""
        ]);
    }