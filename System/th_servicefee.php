<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include ("../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    
    $service = $_REQUEST['service'];
    $isCheck = $_REQUEST['isCheck'];

    $sql = "SELECT * FROM parameters WHERE compcode = '$company' AND ccode = 'SERVICE_FEE'";
    $query = mysqli_query($con, $sql);

    if(mysqli_num_rows($query) != 0){
        $sql = "UPDATE parameters SET `cvalue` = '$service', `nallow` = '$isCheck' WHERE compcode = '$company' AND ccode = 'SERVICE_FEE'";
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
        
    } else {
        $sql = "INSERT INTO parameters (`compcode`, `ccode`, `cvalue`, `norder`, `nallow`, `cstatus`) VALUES ('$company', 'SERVICE_FEE', '$service', 1, '$isCheck', 'ACTIVE')";
        if(mysqli_query($con, $sql)){
            echo json_encode([
                'valid' => true,
                'msg' => "Create a Service fee Successfully"
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'msg' => "Unsuccessfully Create a Service Fee"
            ]);
        }
       
    }