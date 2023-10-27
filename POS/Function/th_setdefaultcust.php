<?php 

    if(!isset($_SESSION)){
        session_start();
    }
    require_once "../../Connection/connection_string.php";
    $company = $_SESSION['companyid'];
    
    $customer = $_REQUEST['customer'];

    $sql = "SELECT * FROM parameters WHERE compcode = '$company' AND ccode = 'BASE_CUSTOMER_POS'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $sql = "UPDATE parameters SET `cvalue` = '$customer', `cstatus` = 'ACTIVE' WHERE compcode = '$company' AND ccode = 'BASE_CUSTOMER_POS'";

        if(mysqli_query($con, $sql))
            echo json_encode([
                'valid' => true,
                'msg' => "Update"
            ]);

    } else {
        $sql = "INSERT INTO parameters (compcode, ccode, cvalue, norder, nallow, cstatus)
        VALUES ('$company', 'BASE_CUSTOMER_POS', '$customer', 1, 0, 'ACTIVE')";
        if(mysqli_query($con, $sql))
            echo json_encode([
                'valid' => true,
                'msg' => "Insert"
            ]);
    }
   
        


    