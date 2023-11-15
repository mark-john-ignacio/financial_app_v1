<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once  "../vendor2/autoload.php";
    include ("../Connection/connection_string.php");
    $company_code = $_SESSION['companyid'];
    $monthcut = $_REQUEST["month"];
    $yearcut = $_REQUEST["year"];

    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);
    
    $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry FROM apv a 
    LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
    LEFT JOIN (
        SELECT DISTINCT(a.ctranno), a.cvatcode, a.compcode from apv_d a
            LEFT JOIN apv b on a.compcode = b.compcode AND a.ctranno = b.ctranno
            WHERE a.compcode ='$company_code' 
            AND MONTH(STR_TO_DATE(b.dapvdate, '%Y-%m-%d')) = $monthcut 
            AND YEAR(STR_TO_DATE(b.dapvdate, '%Y-%m-%d')) = $yearcut 
            AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled =0 
            AND a.ctranno in (
                SELECT capvno FROM paybill a 
                LEFT JOIN paybill_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
            )
        ) c on a.compcode = c.compcode AND a.ctranno = c.ctranno
    WHERE a.compcode ='$company_code' 
    AND MONTH(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $monthcut 
    AND YEAR(STR_TO_DATE(a.dapvdate, '%Y-%m-%d')) = $yearcut 
    AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled =0 
    AND c.cvatcode <> 'NT'
    AND a.ctranno in (
        SELECT capvno FROM paybill a 
        LEFT JOIN paybill_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
    )";
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
