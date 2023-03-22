<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

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
	$nGross = str_replace(",","",$_REQUEST['txtnGross']); 
	$nNet = str_replace(",","",$_REQUEST['txtnNetVAT']); 
	$nVAT = str_replace(",","",$_REQUEST['txtnVAT']); 

	$cCustSI = $_REQUEST['txtSuppSI'];
	$cCustRR = $_REQUEST['txtrefrr'];

	$CurrCode = $_REQUEST['selbasecurr'];
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);

	
	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$AccntCode = $rowaccnt['cacctcode'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//UPDATE HEADER

	if (!mysqli_query($con,"Update suppinv set `ccode` ='$cCustID', `cremarks`=$cRemarks, `dreceived`=STR_TO_DATE('$dDelDate', '%m/%d/%Y'),`ngross`='$nGross', `nbasegross` = '$BaseGross', `nnet` = '$nNet', `nvat` = '$nVAT', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `ccustacctcode`='$AccntCode', `lapproved` = 0, `crefsi` = '$cCustSI', `crefrr` = '$cCustRR' Where compcode='$company' and ctranno='$cSINo'")){
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



?>
