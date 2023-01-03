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


	$cSINo = $_REQUEST['id'];
	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']);
	$nGross = $_REQUEST['ngross'];

	$preparedby = $_SESSION['employeeid'];

	
	//INSERT HEADER

				$sqlhead = mysqli_query($con,"Select cacctcodesales, cvattype from customers where compcode='$company' and cempid='$cCustID'");
				if (mysqli_num_rows($sqlhead)!=0) {
					$row = mysqli_fetch_assoc($sqlhead);
					$cacctcode = "'".$row["cacctcodesales"]."'";
					$cvatcode = "'".$row["cvattype"]."'";
				}

	if (!mysqli_query($con, "UPDATE aradj set `ccode` = '$cCustID', `cremarks` = $cRemarks, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `cacctcode` = $cacctcode, `cvatcode` = $cvatcode where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		echo "False";
	} 
	else {
		echo $cSINo;
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','CREDIT MEMO','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from aradj_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from aradj_t_info Where compcode='$company' and ctranno='$cSINo'");


?>