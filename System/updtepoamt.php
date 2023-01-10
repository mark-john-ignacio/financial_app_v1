<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];
	$x = $_REQUEST['val'];
	$y = $_REQUEST['id'];
	
	
			if (!mysqli_query($con,"UPDATE purchase_approvals set `namount` = '$x' where `compcode` = '$company' and `nlevel` = '$y'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$y','$preparedby',NOW(),'UPDATED','PO APPROVALS','$compname','Update level amount')");

				echo "True";
			}
		





?>
