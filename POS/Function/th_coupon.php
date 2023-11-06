<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $coupon = mysqli_real_escape_string($con, $_REQUEST['coupon']);

    $sql = "SELECT * FROM coupon WHERE `compcode` = '$company' AND `CouponNo` = '$coupon'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0) {
        $row = $query -> fetch_assoc();

        $status = $row['status'];
        $approve = $row['approved'];
        $expired = $row['expired'];
        if($approve != 1){
            json_encode([
                'valid' => false,
                'msg' => "Coupon was not Approved yet!"
            ]);

        } else {
            if($expired < date("Y-m-d")){
                echo json_encode([
                    'valid' => false,
                    'msg' => "Coupon has been Expired"
                ]);
            } else {
                echo match ($status) {
                    "ACTIVE" => json_encode([
                            'valid' => true,
                            'msg' => "Coupon has been Successfully Added"
                        ]),
                    "INACTIVE" => json_encode([
                        'valid' => false,
                        'msg' => "Coupon was not Activated!"
                    ]),
                    "CLAIMED" => json_encode([
                        'valid' => false,
                        'msg' => "Coupon was CLAIMED!"
                    ])
                };
            }
        }
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Coupon entered not found!"
        ]);
        
    }