<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$sign = $_REQUEST['csign'];
		
	if (!mysqli_query($con,"UPDATE mrp_operators set `csign` = NULL where `compcode` = '$company' and `nid` = '$code'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{

		$compname = php_uname('n');
		$preparedby = $_SESSION['employeeid'];

		unlink($sign); 
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$code','$preparedby',NOW(),'UPDATED','MES (EMPLOYEES - REMOVE SIGN)','$compname','Updated Record')");
				
		echo "True";

	}
		

?>
