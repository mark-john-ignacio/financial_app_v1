<?php 
    if(!isset($_SESSION)) {
        session_start();
    }

    include "../../../Connection/connection_string.php";
    include "../../../Model/helper.php";

    $company = $_SESSION['companyid'];
    $month = date("m", strtotime($_POST['months']));
    $year = date("Y", strtotime($_POST['years']));

    $companies = [];
    $sql = "SELECT * FROM company WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    while($list = $query -> fetch_assoc()) {
        $companies = [
            'name' => $list['compname'],
            'trade' => $list['compdesc'],
            'address' => $list['compadd'],
            'tin' => TinValidation($list['comptin'])
        ];
    }

    $sql = "SELECT a.cewtcode, a.newtamt, a.ctranno, a.namount, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin FROM receipt_sales_t a
        LEFT JOIN receipt b on a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN customers c on a.compcode = c.compcode AND b.ccode = c.cempid
        WHERE a.compcode = '$company' AND MONTH(b.dcutdate) = '$month' AND YEAR(b.dcutdate) = '$year'";
    $query = mysqli_query($con, $sql);

    $array = [];

    while($list = $query -> fetch_assoc()) {
        $code = $list['cewtcode'];
        $ewt = getEWT($code);

        if($ewt['valid']) {
            $json = [
                'name' => $list['cname'],
                'address' => $list['chouseno'] . " " . $list['ccity'],
                'tin' => $list['ctin'],
                'tranno' => $list['ctranno'],
                'gross' => $list['namount'],
                'credit' => $list['newtamt'],
                'date' => $list['dcutdate'],
                'ewt' => $ewt['code'],
                'rate' => $ewt['rate']
            ];
            array_push($array, $json);
        }
    }

    if(!empty($array)) {
        echo json_encode([
            'valid' => true,
            'data' => $array,
            'company' => $companies
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Referrence not found!",
            'company' => $companies
        ]);
    }