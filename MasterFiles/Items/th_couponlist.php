<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include ("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $coupon = mysqli_real_escape_string($con, $_REQUEST['coupon']);

    $sql = "SELECT A.*, B.cacctdesc FROM coupon A left join accounts B on A.compcode=B.compcode and A.cacctcode=B.cacctid WHERE A.compcode = '$company' AND A.CouponNo = '$coupon' ORDER BY A.ddate ASC";

    $data = [];
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0) {
        while($row = $query -> fetch_assoc()){
            array_push($data, $row);
        }

        echo json_encode([
            'valid' => true,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Record"
        ]); 
    }