<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];


	$cSINo = $_REQUEST['id'];
	$cCustID = $_REQUEST['ccode'];
	$nGross = str_replace(",","",$_REQUEST['ngross']);

	$ccontname = $_REQUEST['ccontname'];
	$ccontdesg = $_REQUEST['ccontdesg'];
	$ccontdept = $_REQUEST['ccontdept'];
	$ccontemai = $_REQUEST['ccontemai'];
	$ccontsalt = $_REQUEST['ccontsalt'];
	$cvattyp = $_REQUEST['cvattyp'];
	$cterms = $_REQUEST['cterms'];
	$cdelinfo = $_REQUEST['cdelinfo'];
	$cservinfo = $_REQUEST['cservinfo'];

	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = $_REQUEST['crem']; 
	$cSITyp= $_REQUEST['selsityp'];

	$CurrCode = $_REQUEST['currcode']; 
	$CurrDesc = $_REQUEST['currdesc'];  
	$CurrRate= $_REQUEST['currrate']; 
	$BaseGross= str_replace(",","",$_REQUEST['basegross']);

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER

	if (!mysqli_query($con, "UPDATE quote set `ccode` = '$cCustID', `ccontactname` = '$ccontname', `ccontactdesig` = '$ccontdesg', `ccontactdept` = '$ccontdept', `ccontactemail` = '$ccontemai', `ccontactsalut` = '$ccontsalt', `cvattype` = '$cvattyp', `cterms` = '$cterms', `cdelinfo` = '$cdelinfo', `cservinfo` = '$cservinfo', `cremarks` = '$cRemarks', `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `csalestype` = '$cSITyp', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `nbasegross` = $BaseGross where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		echo "False";

		//print_r(mysqli_error($con));
	} 
	else {
		echo $cSINo;
	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','QUOTATION','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from quote_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from quote_t_info Where compcode='$company' and ctranno='$cSINo'");


?>