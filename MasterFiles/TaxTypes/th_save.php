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
	$taxrems = $_REQUEST['taxrems'];
	$taxrate = $_REQUEST['taxrate'];
	$taxtype = $_REQUEST['taxtype'];
	
	if($code=="new"){
		
		if (!mysqli_query($con,"INSERT INTO vatcode (`compcode`,`cvatcode`,`cvatdesc`,`cremarks`,`nrate`,`ctype`) values ('$company','$taxcode','$taxdesc','$taxrems','$taxrate','$taxtype')")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{
			$last_row = mysqli_insert_id($con);

			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$last_row','$preparedby',NOW(),'INSERTED','TAX TYPE','$compname','Inserted New Record')");

			echo "True";
		}
	}
	else{
	
		if (!mysqli_query($con,"UPDATE vatcode set cvatdesc = '$taxdesc', cremarks = '$taxrems', nrate = '$taxrate', ctype = '$taxtype' where `compcode` = '$company' and `nidentity` = '$code'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{
									
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$company','$code','$preparedby',NOW(),'UPDATED','TAX TYPE','$compname','Update Record')");
			
			echo "True";
		}
		
	}

?>
