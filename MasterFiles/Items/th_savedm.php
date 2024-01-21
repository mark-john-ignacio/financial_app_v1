<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    
    include('../../Connection/connection_string.php');
    $company = $_SESSION['companyid'];

    $month = date('m');
    $year = date('y');
    $compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

    $remarks = $_REQUEST['remarks'];
    $label = $_REQUEST['label'];
    $effect = date("Y-m-d", strtotime($_REQUEST['effect']));
    $due = date("Y-m-d", strtotime($_REQUEST['due']));
    $date = date("Y-m-d H:i:s");
    $acctcode = $_REQUEST['acctcode'];

    $sql = "SELECT * FROM discountmatrix where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By `tranno` desc LIMIT 1";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query)==0) {
        $code = "DM".$month.$year."00000";
    } else {
        while($row = $query -> fetch_assoc()){
            $last = $row['tranno'];
        }
        
        
        if(substr($last,2,2) <> $month){
            $code = "DM".$month.$year."00000";
        }
        else{
            $baseno = intval(substr($last,6,5)) + 1;
            $zeros = 5 - strlen($baseno);
            $zeroadd = "";
            
            for($x = 1; $x <= $zeros; $x++){
                $zeroadd = $zeroadd."0";
            }
            
            $baseno = $zeroadd.$baseno;
            $code = "DM".$month.$year.$baseno;
        }
    }


    $sql = "INSERT INTO discountmatrix (`compcode`, `tranno`, `remarks`, `label`, `deffective`, `ddue`, `approved`, `cancelled`, `status`, `ddate`, `cacctcode`) VALUES ('$company', '$code', '$remarks', '$label', '$effect', '$due', 0, 0 ,'ACTIVE', '$date', '$acctcode')";
    
    if(!mysqli_query($con, $sql)){
        printf("Errormessage: %s\n", mysqli_error($con));
    } else {
        mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$code','$preparedby',NOW(),'INSERTED','DISCOUNT-MATRIX','$compname','Inserted New Record')");
        echo json_encode([
            'valid' => true,
            'tranno' => $code,
            'msg' => "Successfully Inserted"
        ]);
    }