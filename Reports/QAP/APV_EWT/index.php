<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    $company = $_SESSION['companyid'];
    include "../../../Connection/connection_string.php";
    include "../../../Model/helper.php";
    $month = date("m", strtotime($_REQUEST['months']));
    $year = date("Y", strtotime($_REQUEST['years']));

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    $list = $query -> fetch_assoc();
    $company_detail = [
        'name' => $list['compname'],
        'trade' => $list['compdesc'],
        'address' => $list['compadd'],
        'tin' => TinValidation($list['comptin'])
    ];

    $apv = [];
    $sql = "SELECT a.ncredit, a.cewtcode, a.ctranno, b.ngross, b.dapvdate, c.cname, c.chouseno, c.ccity, c.ctin FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = b.compcode AND b.ccode = c.ccode 
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) = '$month' AND YEAR(b.dapvdate) = '$year'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $ewt = $list['cewtcode'];
        $credit = floatval($list['ncredit']);
        if(strlen($ewt) != 0 && $credit != 0){
            $ewt = getEWT($ewt);
            if($ewt['valid']) {
                $json = [
                    'name' => $list['cname'],
                    'tin' => $list['ctin'],
                    'credit' => $credit,
                    'ewt' => $ewt['code'],
                    'rate' => $ewt['rate'],
                    'date' => $list['dapvdate'],
                    'tranno' => $list['ctranno'],
                    'address' => $list['chouseno'] . " " . $list['ccity'],
                    'gross' => $list['ngross']
                ];

            $apv[] = $json;
            }
        }
        
    }

    if(!empty($apv)) {
        echo json_encode([
            'valid' => true,
            'data' => $apv,
            'company' => $company_detail
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Reference Found!",
            'company' => $company_detail
        ]);
    }