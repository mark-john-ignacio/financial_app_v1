<?php
    if(!isset($_SESSION)){
        session_start();
    }
    include ("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    $coupon = mysqli_real_escape_string($con, $_REQUEST['coupon']);

    $sql = "SELECT * FROM coupon WHERE `compcode` = '$company' AND `CouponNo` = '$coupon'";
    $query = mysqli_query($con, $sql);

    if(mysqli_num_rows($query) != 0){
        $sql = "UPDATE coupon SET `status` = 'ACTIVE' WHERE `compcode` = '$company' AND `CouponNo` = '$coupon' AND `status` = 'INACTIVE'";
        if(mysqli_query($con, $sql)){
            
            echo json_encode([
                'valid' => true,
                'msg' => "Coupon is now Activated"
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'msg' => "Coupon was already activated"
            ]);
        }

        
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Coupon Not Found"
        ]);
    }