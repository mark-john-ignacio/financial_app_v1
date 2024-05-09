<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$name = $_REQUEST['nme'];
	$desc = mysqli_real_escape_string($con, $_REQUEST['desc']);
	$add = mysqli_real_escape_string($con, $_REQUEST['add']);
	$tin = mysqli_real_escape_string($con,  $_REQUEST['tin']);
	$vat = $_REQUEST['vatz']; 
	$RDOC = $_REQUEST['rdoc']; 
	$BUSTY = $_REQUEST['busty']; 
	$email = mysqli_real_escape_string($con, $_REQUEST['email']);
	$cpnum = mysqli_real_escape_string($con, $_REQUEST['cpnum']);
	$czip = $_REQUEST['zip'];
	$ptucode = $_REQUEST['ptucode'];
	$ptudate = $_REQUEST['ptudate'];
	//$txthdr = $_REQUEST['txthdr']; 

	//, `txtheader` = '$txthdr'


	if (!mysqli_query($con,"UPDATE company set `compname` = '$name', `compdesc` = '$desc', `compadd` = '$add', `comptin` = '$tin', `compvat` = '$vat', `compzip` = '$czip', `email` = '$email', `cpnum` = '$cpnum', `ptucode` = '$ptucode', `ptudate` = '$ptudate', `comprdo` = '$RDOC', `compbustype` = '$BUSTY' where `compcode` = '$company'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{										
			mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$company','$company','$preparedby',NOW(),'UPDATED','COMPANY DETAILS','$compname','Updated Company Detail')");
			
		echo "True";
	}
		
?>
