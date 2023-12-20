<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    $company = $_SESSION['companyid'];
    include "../../../Connection/connection_string.php";
    include "../../../Model/helper.php";
    $month_text = $_REQUEST['months'];
    $month = date("m", strtotime($_REQUEST['months']));
    $year = date("Y", strtotime($_REQUEST['years']));
    $quartersAndMonths = getQuartersAndMonths($year);

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
    foreach ($quartersAndMonths as $quarter => $month) {
        $QUARTERDATA = dataquarterly($month);
        if ($QUARTERDATA['valid']) {
            foreach($QUARTERDATA['quarter'] as $row) {
                $list = $row['data'];
                $code = $list['cewtcode'];
                $credit = $list['ncredit'];
                $gross = $list['ngross'];
                $ewt = getEWT($code);
                if (ValidateEWT($code) && $credit != 0 && $ewt['valid']) {
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