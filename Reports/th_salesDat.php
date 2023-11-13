<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once  "../vendor2/autoload.php";
    include ("../Connection/connection_string.php");
    $company_code = $_SESSION['companyid'];
    // $datecut = date("m", strtotime($_REQUEST["date"]));
    $datecut = date("m", strtotime("August"));
    $sales = [];

    $sql =  "SELECT * FROM company WHERE compcode = '$company_code'";
    $query = mysqli_query($con, $sql);
    $company = $query -> fetch_array(MYSQLI_ASSOC);
    
    $sql = "SELECT * FROM sales a WHERE a.compcode = '$company_code' AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $datecut";
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
