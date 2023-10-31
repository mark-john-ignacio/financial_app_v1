<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$stat = $_REQUEST['stat'];
    $date = date("Y-m-d");
    $compname = php_uname('n');
    $preparedby = $_SESSION['employeeid'];

    $now = date("Y-m-d");
    $days = 0;
    $status = "";
    $approved = 0;
    
    $sql = "SELECT * FROM coupon WHERE `compcode` = '$company' AND `CouponNo` = '$code' ORDER BY `ddate` LIMIT 1";
    // $sql = "SELECT * FROM coupon WHERE `compcode` = '$company' AND `CouponNo` = '$coupon' AND `status` = 'INACTIVE' ORDER BY `ddate` LIMIT 1";
    $query = mysqli_query($con, $sql);
    $row = $query -> fetch_assoc();
    $days = $row['days'];
    $status = $row['status'];
    $approved = $row['approved'];
            
    if(mysqli_num_rows($query) != 0){
        $expired = strtotime($now);
        $expired = strtotime("+$days day", $expired);
        $expired = date("Y-m-d", $expired);
        
        if($approved == 0){
            echo json_encode([
                'valid' => false,
                'msg' => "Coupon was not approved yet"
            ]);
        } else {
            $validStatus = couponStatus($status);

            if($validStatus['valid']){
                $sql = "UPDATE coupon SET `status` = '$stat', `effective` = '$now', `expired` = '$expired' WHERE `compcode` = '$company' AND `CouponNo` = '$code'";
                if(mysqli_query($con, $sql)){
                    
                    mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
                    values('$company','$code','$preparedby',NOW(),'UPDATED','COUPON','$compname','Updated Record')");
                    
                    echo json_encode([
                        'valid' => true,
                        'msg' => "Coupon is now Activated"
                    ]);
                } else {
                    echo json_encode([
                        'valid' => false,
                        'msg' => "adsadad"
                    ]);
                }
            } else {
                echo json_encode([
                    'valid' => false,
                    'msg' => $validStatus['msg']
                ]);
            }
        }
        
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Coupon Not Found"
        ]);
    }
		
    function couponStatus($status){
        $msg = match($status){
            "ACTIVE" => "Coupon was already activated",
            "CLAIMED" => "Coupon was already claimed",
            "INACTIVE" => ""
        };

        $valid = match($status){
            "ACTIVE" => false,
            "CLAIMED" => false,
            "INACTIVE" => true
        };

        return [
            'valid' => $valid,
            'msg' => $msg
        ];
    }
