<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['id'];

	$taxcode = $_REQUEST['taxcode'];
	$taxdesc = $_REQUEST['taxdesc'];
	$taxrate = $_REQUEST['taxrate'];
	
	if($code=="new"){
		
		if (!mysqli_query($con,"INSERT INTO wtaxcodes (`compcode`,`ctaxcode`,`cdesc`,`nrate`) values ('$company','$taxcode','$taxdesc','$taxrate')")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{
			$last_row = mysqli_insert_id($con);

			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$taxcode','$preparedby',NOW(),'INSERTED','EWT CODE','$compname','Inserted New Record')");

			echo "True";
		}
	}
	else{
	
		if (!mysqli_query($con,"UPDATE wtaxcodes set cdesc = '$taxdesc', nrate = '$taxrate' where `compcode` = '$company' and `nident` = '$code'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{
									
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$taxcode','$preparedby',NOW(),'UPDATED','EWT CODE','$compname','Update Record')");
			
			echo "True";
		}
		
	}

?>
