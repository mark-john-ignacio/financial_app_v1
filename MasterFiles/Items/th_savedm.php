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
    $effect = $_REQUEST['effect'];
    $due = date("Y-m-d", strtotime($_REQUEST['due']));
    $matrix = $_REQUEST['pm'];


    $sql = "SELECT * FROM discountmatrix WHERE compcode = '$company' AND YEAR(deffective) = YEAR(CURDATE()) ORDER BY tranno DESC";
    $query = mysqli_query($con, $sql);

    if(mysqli_num_rows($query) != 0){
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
    } else {
        $code = "DM".$month.$year."00000";
    }

    $sql = "INSERT INTO discountmatrix (`compcode`, `tranno`, `remarks`, `label`, `matrix`, `deffective`, `ddue`, `approved`, `cancelled`, `status`) 
            VALUES ('$company', '$code', '$remarks', '$label', '$matrix', '$effect', '$due', 0, 0 ,'ACTIVE')";
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