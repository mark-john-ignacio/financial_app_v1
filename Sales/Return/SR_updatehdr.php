<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];


function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


	$cSINo = $_REQUEST['txtcsalesno'];
	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']);

	/*
	$nGross = str_replace(",","",$_REQUEST['txtnGross']);

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);
*/
	$preparedby = $_SESSION['employeeid'];

	
	//INSERT HEADER , `ngross` = '$nGross', `nbasegross` = '$BaseGross', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate'

	if (!mysqli_query($con, "UPDATE salesreturn set `ccode` = '$cCustID', `cremarks` = $cRemarks, `dreceived` = STR_TO_DATE('$dDelDate', '%m/%d/%Y') where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		echo "False";
	} 
	else {
		echo $cSINo;

		//INSERT LOGFILE
		$compname = php_uname('n');
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'UPDATED','SALES RETURN','$compname','Updated Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from salesreturn_t Where compcode='$company' and ctranno='$cSINo'");
	}
	

?>