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
    return "'".str_replace("'","\'",$valz)."'";
	}
}


	$cSINo = $_REQUEST['txtcsalesno'];
	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$nGross = str_replace(",","",$_REQUEST['txtnGross']);
	$cSITyp = $_REQUEST['selsityp'];
	$cCPONo = $_REQUEST['txtcPONo'];

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);

	$salesman = $_REQUEST['txtsalesmanid'];
	$delcodes = $_REQUEST['txtdelcustid'];
	$delhousno = chkgrp($_REQUEST['txtchouseno']);
	$delcity = chkgrp($_REQUEST['txtcCity']);
	$delstate = chkgrp($_REQUEST['txtcState']);
	$delcountry = chkgrp($_REQUEST['txtcCountry']);
	$delzip = $_REQUEST['txtcZip'];
	$specins = chkgrp($_REQUEST['txtSpecIns']);
	
	$preparedby = $_SESSION['employeeid']; 
	
	//INSERT HEADER

	if (!mysqli_query($con, "UPDATE so set `ccode` = '$cCustID', `cremarks` = $cRemarks, `cspecins` = $specins, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross',  `nbasegross` = '$BaseGross', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `csalestype` = '$cSITyp', `cpono` = '$cCPONo', `csalesman` = '$salesman', `cdelcode` = '$delcodes', `cdeladdno` = $delhousno, `cdeladdcity` = $delcity, `cdeladdstate` = $delstate, `cdeladdcountry` = $delcountry, `cdeladdzip` = '$delzip'  where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		//print_r( mysqli_error($con));
		echo "False";
	} 
	else {
		echo $cSINo;
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','SALES ORDER','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from so_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from so_t_info Where compcode='$company' and ctranno='$cSINo'");


?>