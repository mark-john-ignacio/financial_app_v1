<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');
require_once('../../Model/helper.php');

$company = $_SESSION['companyid'];


	$cSINo = $_REQUEST['txtcsalesno'];
	$cCustID = $_REQUEST['txtcustid'];
	$cCustIDel = $_REQUEST['txtcustiddel'];

	$nGross = str_replace(",","",$_REQUEST['txtnGross']);

	$ccontname = $_REQUEST['txtcontactname'];
	$ccontdesg = $_REQUEST['txtcontactdesig'];
	$ccontdept = $_REQUEST['txtcontactdept'];
	$ccontemai = $_REQUEST['txtcontactemail'];

	$ccontsalt = str_replace("'","\'",$_REQUEST['txtcontactsalut']);
	
	$cacceptby = $_REQUEST['txtaccpetby'];

	$cvattyp = $_REQUEST['selvattype'];
	if(isset($_REQUEST['selterms'])){
		$cterms = $_REQUEST['selterms'];
	}else{
		$cterms = "";
	}
	
	$cdelinfo = $_REQUEST['txtdelinfo'];
	$cservinfo = $_REQUEST['txtservinfo'];

	$dDelDate = $_REQUEST['date_delivery'];
	$dQuoteDate = $_REQUEST['date_trans'];
	$cRemarks = $_REQUEST['txtremarks']; 
	$cSITyp= $_REQUEST['selsityp'];
	$cQOTyp= $_REQUEST['selqotyp'];

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['currdesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER

	if (!mysqli_query($con, "UPDATE quote set `ccode` = '$cCustID', `cdelcode` = '$cCustIDel', `ccontactname` = '$ccontname', `ccontactdesig` = '$ccontdesg', `ccontactdept` = '$ccontdept', `ccontactemail` = '$ccontemai', `ccontactsalut` = '$ccontsalt', `cvattype` = '$cvattyp', `cterms` = '$cterms', `cdelinfo` = '$cdelinfo', `cservinfo` = '$cservinfo', `cremarks` = '$cRemarks', `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `csalestype` = '$cSITyp', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `nbasegross` = $BaseGross, `dtrandate` = STR_TO_DATE('$dQuoteDate', '%m/%d/%Y'), `cacceptedby` = '$cacceptby', `quotetype` = '$cQOTyp' where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
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


	if(count($_FILES) != 0){
		$directory = "../../Components/assets/QO/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory, count($_FILES)-1);
	}
?>