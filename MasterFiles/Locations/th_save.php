<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$desc = strtoupper($_REQUEST['desc']);
	
	if($code=="new"){
		
			if (!mysqli_query($con,"INSERT INTO locations (`compcode`,`cdesc`) values ('$company','$desc')")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				$last_row = mysqli_insert_id($con);

				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$last_row','$preparedby',NOW(),'INSERTED','SECTION','$compname','Inserted New Record')");

				echo "True";
			}
	}
	else{
	
			if (!mysqli_query($con,"UPDATE locations set cdesc = '$desc' where `compcode` = '$company' and `nid` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
										
				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$code','$preparedby',NOW(),'UPDATED','SECTION','$compname','Update Record')");
				
				echo "True";
			}
		
	}

?>
