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
    
    $sql = "SELECT a.*,b.cname, b.ctradename, b.czip, b.chouseno, b.ccity, b.ccountry, b.cstate, b.ctin, b.cvattype FROM sales a 
        LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
        WHERE a.compcode = '$company_code' 
        AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $monthcut 
        AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $yearcut  
        AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0
        AND b.cvattype != 'NV'
        AND a.ctranno in (
            SELECT b.csalesno FROM receipt a 
            left join receipt_sales_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                        WHERE a.compcode = '$company_code' 
                        AND a.lapproved = 1 
                        AND a.lvoid = 0 
                        AND a.lcancelled = 0
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
