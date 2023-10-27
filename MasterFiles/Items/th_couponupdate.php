<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    $coupon = mysqli_real_escape_string($con, $_REQUEST['tranno']);
    $label = mysqli_real_escape_string($con, $_REQUEST['label']);
    $remarks = mysqli_real_escape_string($con, $_REQUEST['remarks']);
    $price = mysqli_real_escape_string($con, $_REQUEST['priced']);
    $barcode = mysqli_real_escape_string($con, $_REQUEST['barcode']);
    $expired = mysqli_real_escape_string($con, $_REQUEST['expired']);

    $sql = "UPDATE coupon SET `label` = '$label', `remarks` = '$remarks', `price` = '$price', `barcode` = '$barcode', `expired` = '$expired' WHERE compcode = '$company' AND CouponNo = '$coupon'";
    if(mysqli_query($con, $sql)){
        $compname = php_uname('n');
        $preparedby = $_SESSION['employeeid'];

        mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
        values('$company','$coupon','$preparedby',NOW(),'UPDATED','COUPON','$compname','Updated Record')");

        echo json_encode([
            'valid' => true,
            'msg' => "Coupon has successfully update!"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Coupon has not been update!"
            
        ]); 
    }
     