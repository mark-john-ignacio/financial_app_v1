<?php
if(!isset($_SESSION)){
session_start();
}

require_once "../../Connection/connection_string.php";

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


	
	if (!mysqli_query($con,"Update purchase set lapproved=1 where compcode='$company' and cpono='$tranno'")) {
		echo "False";
	} 
	else {
		echo "True";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$tranno','$preparedby',NOW(),'POSTED','PURCHASE ORDER','$compname','Post Record')");

	}



?>
