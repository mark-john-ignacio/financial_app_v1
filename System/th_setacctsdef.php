<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$valz = $_REQUEST['id'];
		
			if (!mysqli_query($con,"UPDATE accounts_default set `cacctno` = '$valz' where `compcode` = '$company' and `ccode` = '$code'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

					$compname = php_uname('n');
					$preparedby = $_SESSION['employeeid'];
					
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$code','$preparedby',NOW(),'UPDATED','ACCOUNTS (".$code.")','$compname','Updated Record')");
					
					
					echo "True";

			}
		

?>
