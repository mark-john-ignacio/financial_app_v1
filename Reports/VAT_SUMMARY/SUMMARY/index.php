<?php
    if(!isset($_SESSION)) {
        session_start();
    }
    
    include "../../../Connection/connection_string.php";
    $company = $_SESSION['companyid'];

    $sql = "SELECT * FROM report_vat_summary WHERE compcode = '$company'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) !=  0) {
        while($list = $query -> fetch_assoc()) {
            switch($list['code']) {
                case "ZR":
                    $zero = $list['taxcode'];
                    break;
                case "GOV":
                    $gov = $list['taxcode'];
                    break;
                case "VE":
                    $exempt = $list['taxcode'];
                    break;
                case "VT":
                    $vatable = $list['taxcode'];
                    break;
            }
        }

        echo json_encode([
            'valid' => true,
            'zero' => $zero,
            'gov' => $gov,
            'exempt' => $exempt,
            'vatable' => $vatable
        ]);
    } else {
        echo json_encode([
            'valid' => false,
        ]);
    }
    