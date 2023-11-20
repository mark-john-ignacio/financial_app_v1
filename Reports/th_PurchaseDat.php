<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once  "../vendor2/autoload.php";
    include ("../Connection/connection_string.php");
    $company_code = $_SESSION['companyid'];
    $monthcut = $_REQUEST["month"];
    $yearcut = $_REQUEST["year"];
    $code = $_REQUEST['vatcode'];

    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);
 
    $sql = "SELECT a.cacctno FROM accounts_default a WHERE a.compcode = '$company_code' AND a.ccode = 'PURCH_VAT' ORDER BY a.cacctno DESC LIMIT 1";
    $query = mysqli_query($con, $sql);
    $account = $query -> fetch_array(MYSQLI_ASSOC);
    $vat_code = $account['cvattype'];

    // $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry FROM apv a 
    //             LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
    //             WHERE a.compcode ='$company_code' 
    //             AND MONTH(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $monthcut 
    //             AND YEAR(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $yearcut
    //             AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled =0 
    //             -- AND c.cvatcode <> 'NT'
    //             AND a.ctranno in (
    //                     SELECT b.capvno FROM paybill a 
    //                     LEFT JOIN paybill_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    //                     LEFT JOIN suppinv c on a.compcode = c.compcode AND c.ctranno = b.capvno
    //                     WHERE a.compcode = '$company_code' AND (c.npaidamount > 0 OR c.npaidamount <> null)
    //             )";

    $sql = "SELECT a.*, b.* FROM paybill a
            LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
            WHERE a.compcode = '$company_code'
            AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
            AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
            AND b.cvattype = '$code'
            AND ctranno in (
                SELECT a.ctranno FROM paybill_t a 
                LEFT JOIN apv_t b on a.compcode = b.compcode AND a.capvno = b.ctranno
                WHERE a.compcode = '$company_code' AND b.cacctno = '$vat_code'
            )
            AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($sales, $row);
    }

    if(!empty($sales)){
        echo json_encode([
            'valid' => true,
            'data' => $sales
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Record Found"
        ]);
    }
?>
