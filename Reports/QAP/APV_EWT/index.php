<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    $company = $_SESSION['companyid'];
    include "../../../Connection/connection_string.php";

    $month = date("m", strtotime($_REQUEST['months']));
    $year = date("Y", strtotime($_REQUEST['years']));

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    $list = $query -> fetch_assoc();
    $company_detail = [
        'name' => $list['compname'],
        'trade' => $list['compdesc'],
        'address' => $list['compadd'],
        'tin' => $list['comptin']
    ];

    $apv = [];
    $sql = "SELECT a.ncredit, a.cewtcode, c.cname, c.ctin FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = b.compcode AND b.ccode = c.ccode 
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) = '$month' AND YEAR(b.dapvdate) = '$year'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $ewt = $list['cewtcode'];
        
        if(strlen($ewt) != 0){
            $ewt = getEWT($ewt);
            if($ewt['valid']) {
                $json = [
                    'name' => $list['cname'],
                    'tin' => $list['ctin'],
                    'credit' => $list['ncredit'],
                    'ewt' => $ewt['code'],
                    'rate' => $ewt['rate']
                ];

            $apv[] = $json;
            }
        }
        
    }

    function getEWT($data) {
        global $con, $company;
        
        $sql = "SELECT ctaxcode, nrate FROM wtaxcodes WHERE compcode = '$company' AND ctaxcode = '$data'";
        $queries = mysqli_query($con, $sql);
        
        if(mysqli_num_rows($queries) !== 0) {
            $fetch = $queries -> fetch_array(MYSQLI_ASSOC);
            return [
                'valid' => true,
                'code' => $fetch['ctaxcode'],
                'rate' => $fetch['nrate'],
            ];
           
        }

        return [
            'valid' => false,
        ];
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