<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


$chkSales = mysqli_query($con,"select * from purchase where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By cpono desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "PO".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['cpono'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "PO".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "PO".$dmonth.$dyear.$baseno;
	}
}

	
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


	$delto = chkgrp($_REQUEST['txtdelcust']); 
	$deladd = chkgrp($_REQUEST['txtdeladd']); 
	$delnotes = chkgrp($_REQUEST['textdelnotes']);
	$billto = chkgrp($_REQUEST['txtbillto']); 
	$cterms = chkgrp($_REQUEST['selterms']); 


	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$AccntCode = $rowaccnt['cacctcode'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER	
	if (!mysqli_query($con,"INSERT INTO purchase(`compcode`, `cpono`, `ccode`, `cremarks`, `ddate`, `dneeded`, `ngross`, `nbasegross`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`, `ccontact`, `ccontactemail`, `cdelto`, `ddeladd`, `ddelinfo`, `cbillto`, `cterms`) values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$BaseGross', '$CurrCode', '$CurrDesc', '$CurrRate', '$preparedby', 0, 0, 0, '$AccntCode', $cContact, $cContactEmail, $delto, $deladd, $delnotes, $billto, $cterms)")){
		echo "False";
	}
	else{

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE ORDER','$compname','Inserted New Record')");
	
	
		// Delete previous details
		mysqli_query($con, "Delete from purchase_t Where compcode='$company' and cpono='$cSINo'");

		echo $cSINo;
	}
	


?>
