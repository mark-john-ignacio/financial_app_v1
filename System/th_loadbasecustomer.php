<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    require_once "../Connection/connection_string.php";
    $company = $_SESSION['companyid'];

    $sql = "SELECT b.cname, b.cpricever FROM parameters a
        LEFT JOIN customers b ON a.compcode = b.compcode AND a.cvalue = b.cempid
        WHERE a.compcode = '$company' AND a.ccode = 'BASE_CUSTOMER_POS' AND a.cstatus = 'ACTIVE'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $row = $query -> fetch_assoc();
        $value = $row['cname'];
        $pm = $row['cpricever'];

        echo json_encode([
            'valid' => true,
            'data' => $value,
            'pm' => $pm
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "No Record Found!"
        ]);
    }