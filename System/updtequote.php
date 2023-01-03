<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];

	$company = $_SESSION['companyid'];
	$x = $_REQUEST['val'];
	$y = $_REQUEST['nme'];

	
	$result = mysqli_query ($con, "Select * From parameters where compcode='$company' and ccode='$y'"); 
	
	if(mysqli_num_rows($result)==0){
		
			if (!mysqli_query($con,"INSERT INTO parameters (`compcode`,`ccode`,`cdesc`,`norder`) values ('$company','$y','$x',1)")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{
				echo "True";
	
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$y','$preparedby',NOW(),'INSERTED','QUOTE PRINTOUT','$compname','Inserted New Record')");

			}
	}
	else {
		
		
			if (!mysqli_query($con,"UPDATE parameters set `cdesc` = '$x' where `compcode` = '$company' and `ccode` = '$y'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{

				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$y','$preparedby',NOW(),'UPDATED','QUOTE PRINTOUT','$compname','Inserted New Record')");

				echo "True";
			}
		
	}




?>
