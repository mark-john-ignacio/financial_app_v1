<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}

$ctranno = $_REQUEST['ctranno'];
$rr = $_REQUEST['rr'];
$cremarks = chkgrp($_REQUEST['crem']);
$daterec = $_REQUEST['ddate'];
$preparedby = $_SESSION['employeeid'];

//UPDATE HEADER

	if (!mysqli_query($con,"UPDATE receive_putaway set `cremarks` = $cremarks, `dreceived` = STR_TO_DATE('$daterec', '%m/%d/%Y') Where `compcode` = '$company' and `ctranno` = '$ctranno'")){
		
		echo "False";
	}
	else{

	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$ctranno','$preparedby',NOW(),'INSERTED','PUTAWAY','$compname','Updated Record')");


// Delete previous details
		mysqli_query($con, "Delete from receive_putaway_t Where compcode='$company' and ctranno='$ctranno'");

		echo $ctranno;
	}
?>