<?php 
    if(!isset($_SESSION)) {
        session_start();
    }

    include "../../../Connection/connection_string.php";
    include "../../../Model/helper.php";

    $company = $_SESSION['companyid'];
    $month = date("m", strtotime($_REQUEST['months']));
    $year = date("Y", strtotime($_REQUEST['years']));

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


    // $sql = "SELECT a.cewtcode, a.newtamt, a.ctranno, b.ngross, b.dcheckdate, c.cname, c.chouseno, c.ccity, c.ctin FROM paybill_t a 
    //     LEFT JOIN paybill b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    //     LEFT JOIN suppliers c on a.compcode = c.compcode AND b.ccode = c.ccode
    //     WHERE a.compcode = '$company' AND MONTH(b.dcheckdate) = '$month' AND YEAR(b.dcheckdate) = '$year'";

    // $sql = "SELECT a.cewtcode, a.newtamt, a.ctranno, b.namount, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc FROM receipt_sales_t a
    //     LEFT JOIN receipt b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    //     LEFT JOIN customers c on a.compcode = c.compcode AND b.ccode = c.cempid
    //     LEFT JOIN groupings d on a.compcode = b.compcode AND c.ccustomertype = d.ccode
    //     WHERE a.compcode = '$company' AND MONTH(b.dcutdate) = '$month' AND YEAR(b.dcutdate) = '$year' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND d.ctype = 'CUSTYP'";

    $sql = "SELECT a.cewtcode, a.ctranno, b.ngross, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc FROM sales_t a
        LEFT JOIN sales b on a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN customers c on a.compcode = c.compcode AND b.ccode = c.cempid
        LEFT JOIN groupings d on a.compcode = b.compcode AND c.ccustomertype = d.ccode
        WHERE a.compcode = '$company' AND MONTH(b.dcutdate) = '$month' AND YEAR(b.dcutdate) = '$year' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND d.ctype = 'CUSTYP'";
    $query = mysqli_query($con, $sql);

    $array = array();

    while($list = $query -> fetch_assoc()) {
        $code = $list['cewtcode'];
        $ewt = getEWT($code);

        if (ValidateEWT($code) && $ewt['valid']) {
            $json = array(
                'name' => $list['cname'],
                'address' => $list['chouseno'] . " " . $list['ccity'],
                'tin' => $list['ctin'],
                'tranno' => $list['ctranno'],
                'gross' => $list['ngross'],
                'credit' => floatval($list['ngross']) * (floatval($ewt['rate']) / 100),
                'date' => $list['dcutdate'],
                'ewt' => $ewt['code'],
                'rate' => $ewt['rate']
            );
            $array[] = $json;
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