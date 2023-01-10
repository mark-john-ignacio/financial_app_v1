<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "''";
	}else{
    return "'".str_replace("'","\'",$valz)."'";
	}
}

	$cSINo = $_REQUEST['id'];
	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']); 
	$nGross = $_REQUEST['ngross'];
	$nDRPrintNo = chkgrp($_REQUEST['cdrprintno']);

	$salesman = $_REQUEST['salesman'];
	$delcodes = $_REQUEST['delcodes'];
	$delhousno = chkgrp($_REQUEST['delhousno']);
	$delcity = chkgrp($_REQUEST['delcity']);
	$delstate = chkgrp($_REQUEST['delstate']);
	$delcountry = chkgrp($_REQUEST['delcountry']);
	$delzip = $_REQUEST['delzip'];

	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";

				$sqlhead = mysqli_query($con,"Select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'");
				if (mysqli_num_rows($sqlhead)!=0) {
					$row = mysqli_fetch_assoc($sqlhead);
					$cacctcode = "'".$row["cacctcodesales"]."'";
				}
	
	//INSERT HEADER

	if (!mysqli_query($con, "UPDATE ntdr set `ccode` = '$cCustID', `cremarks` = $cRemarks, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `cacctcode` = $cacctcode, `cdrprintno` = $nDRPrintNo, `csalesman` = '$salesman', `cdelcode` = '$delcodes', `cdeladdno` = $delhousno, `cdeladdcity` = $delcity, `cdeladdstate` = $delstate, `cdeladdcountry` = $delcountry, `cdeladdzip` = '$delzip' where `compcode` = '$company' and `ctranno` = '$cSINo'")) {  
		echo "False";
	} 
	else {
		echo $cSINo;
	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','DR NON-TRADE','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from ntdr_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from ntdr_t_info Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from ntdr_t_serials Where compcode='$company' and ctranno='$cSINo'");	

?>