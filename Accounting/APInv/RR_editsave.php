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
	$dDelDate = $_REQUEST['date_received'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$cCustSI = $_REQUEST['txtSuppSI'];
	$cCustRR = $_REQUEST['txtrefrr'];

	$CurrCode = $_REQUEST['selbasecurr'];
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	
	$nnetvat = $_REQUEST['txtnNetVAT']; //VATABLE SALES   nnet
	$nexempt = $_REQUEST['txtnExemptVAT']; //VAT EXEMPT SALES   nexempt
	$nvat = $_REQUEST['txtnVAT']; //VAT   nvat
	$nGrossBefore = $_REQUEST['txtnGrossBef']; //TOTAL GROSS  BEFORE DISCOUNT ngrossbefore
	if(isset($_REQUEST['txtnEWT'])){
		$nLessEWT = $_REQUEST['txtnEWT']; //EWT
	}else{
		$nLessEWT = ""; //EWT
	}
	$nGross = $_REQUEST['txtnGross']; //TOTAL AMOUNT ngross
	$BaseGross= $_REQUEST['txtnBaseGross']; //TOTAL AMOUNT * currency rate    nbasegross

	if(isset($_REQUEST['selewt'])){
		$cewtcode = implode(",",$_REQUEST['selewt']);
	}else{
		$cewtcode = "";
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

	if (!mysqli_query($con,"Update suppinv set `ccode` ='$cCustID', `cremarks`=$cRemarks, `dreceived`=STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `nnet` = '$nnetvat', `nvat` = '$nvat', `nexempt` = '$nexempt', `newt` = '$nLessEWT', `cewtcode` = '$cewtcode', `ngrossbefore` = '$nGrossBefore', `ngross`='$nGross', `nbasegross` = '$BaseGross', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `ccustacctcode`='$AccntCode', `lapproved` = 0, `crefsi` = '$cCustSI', `crefrr` = '$cCustRR' Where compcode='$company' and ctranno='$cSINo'")){
		echo "False";
	}
	else{
		echo $cSINo;
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cSINo','$preparedby',NOW(),'UPDATED','RECEIVING','$compname','Updated Record')");

	// Delete previous details
	mysqli_query($con, "Delete from suppinv_t Where compcode='$company' and ctranno='$cSINo'");


	if(count($_FILES) != 0){
		$directory = "../../Components/assets/RI/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}
?>
