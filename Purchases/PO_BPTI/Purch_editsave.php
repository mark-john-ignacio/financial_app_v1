<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');
require_once('../../Model/helper.php');

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}



	$cSINo = $_REQUEST['txtcpono'];
	$company = $_SESSION['companyid'];

	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_needed'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$cContact = chkgrp($_REQUEST['txtcontactname']); 
	$cContactEmail = chkgrp($_REQUEST['contact_email']); 
	$nGross = str_replace(",","",$_REQUEST['txtnGross']);

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);
	$PayType = $_REQUEST['selpaytype']; 

	$cApprvBy =  mysqli_real_escape_string($con, $_REQUEST['apprby']);
	$cCheckBy =  mysqli_real_escape_string($con, $_REQUEST['chkdby']);

	if(isset($_REQUEST['selterms'])){
		$PayTerms = "'".$_REQUEST['selterms']."'";
	}else{
		$PayTerms = "NULL";
	}

	$delto = chkgrp($_REQUEST['txtdelcust']); 
	$deladd = chkgrp($_REQUEST['txtdeladd']); 
	$delnotes = chkgrp($_REQUEST['textdelnotes']);
	$billto = chkgrp($_REQUEST['txtbillto']); 

	if(isset($_REQUEST['selewt'])){
		$cewtcode = "'".$_REQUEST['selewt']."'";
	}else{
		$cewtcode = "NULL";
	}

	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$AccntCode = $rowaccnt['cacctcode'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//UPDATE HEADER

	if (!mysqli_query($con,"Update purchase set `ccode` ='$cCustID', `cremarks`=$cRemarks, `ccontact`=$cContact, `ccontactemail`=$cContactEmail, `dneeded`=STR_TO_DATE('$dDelDate', '%m/%d/%Y'),`ngross`='$nGross', `ccustacctcode`='$AccntCode', `nbasegross`='$BaseGross', `ccurrencycode`='$CurrCode', `ccurrencydesc`='$CurrDesc', `nexchangerate`='$CurrRate', `ladvancepay` = $PayType, `cterms` = $PayTerms, `cdelto` = $delto, `ddeladd` = $deladd, `ddelinfo` = $delnotes, `cbillto` = $billto, `cewtcode` = $cewtcode, `capprovedby` = '$cApprvBy', `ccheckedby` = '$cCheckBy' Where compcode='$company' and cpono='$cSINo'")){
		echo "False";
	}
	else{
		echo $cSINo;
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cSINo','$preparedby',NOW(),'UPDATED','PURCHASE ORDER','$compname','Updated Record')");

	// Delete previous details
	mysqli_query($con, "Delete from purchase_t Where compcode='$company' and cpono='$cSINo'");


	if(count($_FILES) != 0){
		$directory = "../../Components/assets/PO/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}

?>
