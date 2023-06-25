<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	$company = $_SESSION['companyid'];
	$name = $_REQUEST['nme'];
	$desc = $_REQUEST['desc'];
	$add = $_REQUEST['add'];
	$tin = $_REQUEST['tin'];
	$vat = $_REQUEST['vatz']; 
	$email = $_REQUEST['email'];
	$cpnum = $_REQUEST['cpnum'];
	$czip = $_REQUEST['zip'];
	//$txthdr = $_REQUEST['txthdr']; 

	//, `txtheader` = '$txthdr'


			if (!mysqli_query($con,"UPDATE company set `compname` = '$name', `compdesc` = '$desc', `compadd` = '$add', `comptin` = '$tin', `compvat` = '$vat', `compzip` = '$czip', `email` = '$email', `cpnum` = '$cpnum' where `compcode` = '$company'")) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
			else{										
					mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
					values('$company','$company','$preparedby',NOW(),'UPDATED','COMPANY DETAILS','$compname','Updated Company Detail')");
					
				echo "True";
			}
		
?>
