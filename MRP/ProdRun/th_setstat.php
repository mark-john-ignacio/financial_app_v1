<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	
	$company = $_SESSION['companyid'];
	$processid = $_REQUEST['processid'];
	$colnme = $_REQUEST['colnme'];
	$colval = $_REQUEST['colval'];
		
			if (!mysqli_query($con,"UPDATE mrp_jo_process_t set `$colnme` = '$colval' where `compcode` = '$company' and `nid` = '$processid'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					$compname = php_uname('n');
					$preparedby = $_SESSION['employeeid'];
					
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','PRODUCTION RUN','$compname','Updated Record')");
					
					
					echo "True";

			}
		

?>
