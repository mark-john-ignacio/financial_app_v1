<?php
    if(!isset($_SESSION)){
        session_start();
    }

    include("../../Connection/connection_string.php");
    $company = $_SESSION['companyid'];
    $month = date("m");
    $year = date("y");

    $sql = "SELECT * FROM coupon WHERE compcode = '$company' AND YEAR(ddate) = YEAR(CURDATE()) ORDER BY CouponNo DESC LIMIT 1";
    $query = mysqli_query($con, $sql);

    if (mysqli_num_rows($query)==0) {
        $code = "CP".$month.$year."00000";
    } else {
        while($row = $query -> fetch_assoc()){
            $last = $row['CouponNo'];
        }
        
        
        if(substr($last,2,2) <> $month){
            $code = "CP".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,6,5)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $code = "CP".$month.$year.$baseno;
        }
    }

    /**
     * Initiate Variables
     */

    $label = mysqli_real_escape_string($con, $_REQUEST['label']);
    $remarks = mysqli_real_escape_string($con, $_REQUEST['remarks']);
    $price = mysqli_real_escape_string($con, $_REQUEST['priced']);
    $barcode = mysqli_real_escape_string($con, $_REQUEST['barcode']);
    $days = mysqli_real_escape_string($con, $_REQUEST['days']); 
    $acctcode = mysqli_real_escape_string($con, $_REQUEST['acctcode']);

    $sql = "INSERT INTO coupon (`compcode`, `label`, `remarks`, `CouponNo`, `barcode`, `days`, `price`, `status`, `approved`, `cancelled`, `ddate`, `cacctcode`) VALUES('$company', '$label', '$remarks', '$code', '$barcode', '$days', '$price', 'INACTIVE', 0, 0, NOW(), '$acctcode')";
    if(mysqli_query($con, $sql)){
        echo json_encode([
            'valid' => true,
            'msg' => "Coupon has been saved!"
        ]);
    } else {
        echo json_encode([
            'valid' => false,
            'msg' => "Coupon unable to saved"
        ]);
    }