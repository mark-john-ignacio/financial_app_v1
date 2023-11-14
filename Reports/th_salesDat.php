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
    
    $sql = "SELECT * FROM sales a WHERE a.compcode = '$company_code' AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $monthcut AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $yearcut AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled =0";
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
