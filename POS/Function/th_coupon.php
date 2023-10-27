<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $coupon = mysqli_real_escape_string($con, $_REQUEST['coupon']);

    $sql = "SELECT * FROM coupon WHERE `compcode` = '$company' AND `CouponNo` = '$coupon' AND `status` = 'ACTIVE' AND `approved` = 1";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0) {
        echo json_encode([
            'valid' => true,
            'msg' => "Coupon has been Successfully Added"
        ]);
    } else {
        $sql = "SELECT * FROM coupon WHERE `compcode` = '$company' AND `CouponNo` = '$coupon' AND `status` = 'INACTIVE'";
        $query = mysqli_query($con, $sql);
        if(mysqli_num_rows($query) != 0){

            $row = $query -> fetch_assoc();
            $approve = $row['approved'];
            echo match($approve) {
                '0' => json_encode([
                        'valid' => false,
                        'msg' => "Coupon was not Approved yet!"
                    ]),
                '1' => json_encode([
                    'valid' => false,
                    'msg' => "Coupon was not Activated!"
                ]),
            };
            
        } else {
            echo json_encode([
                'valid' => false,
                'msg' => "Coupon entered not found!"
            ]);
        }
    }