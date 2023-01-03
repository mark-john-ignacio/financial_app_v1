<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];

$cSINo = $_REQUEST["id"]; 
	
	echo $cSINo;
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','INVENTORY COUNT','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from invcount_t Where compcode='$company' and ctranno='$cSINo'");

?>