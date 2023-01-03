<?php
session_start();
include('../Connection/connection_string.php');


	$company = $_SESSION['companyid'];
	$chkno = $_REQUEST['orno'];
	$rem = $_REQUEST['rem'];
	
	$cnewchk = (float)$chkno + 1;
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	
		echo $cnewchk;
		
		//insert in void table
		$sql2 = "INSERT INTO receipt_voids(`compcode`,`cornumber`,`cremarks`,`cvoidby`,`ddatetime`) VALUES('$company','$chkno','$rem','$preparedby',NOW())"; 
		
		if (!mysqli_query($con, $sql2)) {
			if(mysqli_error($con)!=""){
				echo "Error: ".mysqli_error($con)."<br>"."INSERT INTO receipt_voids(`compcode`,`cornumber`,`cremarks`,`cvoidby`,`ddatetime`) VALUES('$company','$chkno','$rem','$preparedby',NOW())";
			}
		}
		
		$compname = php_uname('n');
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values($company','$chkno','$preparedby',NOW(),'VOID','OR NO','$compname','Void OR Number')");

			
			
?>
