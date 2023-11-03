<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include ("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];

    $isCheck = $_REQUEST['isCheck'];

    $sql = "UPDATE parameters SET `nallow` = '$isCheck' WHERE compcode = '$company' AND ccode = 'SERVICE_FEE'";
        if(mysqli_query($con, $sql)){
            echo json_encode([
                'valid' => true,
                'msg' => "Successfully Update Service Fee"
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'msg' => "unsuccesfully Update Service Fee"
            ]);
        }