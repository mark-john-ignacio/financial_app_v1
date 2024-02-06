<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$stat = $_REQUEST['stat'];
	$typ = $_REQUEST['typz'];	

	if($typ=="CHILDSTAT"){

		if (!mysqli_query($con,"UPDATE customers_secondary set `cstatus` = '$stat' where `compcode` = '$company' and `nidentity` = '$code'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{

			$compname = php_uname('n');
			$preparedby = $_SESSION['employeeid'];
			
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','$code','$preparedby',NOW(),'UPDATED','CUST_SECONDARY (".$typ.")','$compname','Updated Record')");
			
			
			echo "True";

		}

	}else{
		
		if (!mysqli_query($con,"UPDATE groupings set `cstatus` = '$stat' where `compcode` = '$company' and `ccode` = '$code' and ctype='$typ'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
		else{

			$compname = php_uname('n');
			$preparedby = $_SESSION['employeeid'];
			
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','$code','$preparedby',NOW(),'UPDATED','CUSTOMERS (".$typ.")','$compname','Updated Record')");
			
			
			echo "True";

		}
	}
		

?>
