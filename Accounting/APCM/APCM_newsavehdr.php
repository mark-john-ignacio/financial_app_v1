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

if($_REQUEST['hdtsavetyp']=="new"){

$chkSales = mysqli_query($con,"select * from apcm where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "CM".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "PC".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "PC".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$nGross = $_REQUEST['txtnamt'];
	
	if(isset($_REQUEST['chkwithref'])){
		$cref = 1;
	}
		else{
			$cref = 0;
			
		}

	$crefno = $_REQUEST['txtcrefno'];

	//$ctype="Credit";
	$preparedby = $_SESSION['employeeid'];
	


	if (!mysqli_query($con, "INSERT INTO apcm(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`,`crefno`,`cwithref`) values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', '$crefno', $cref)")) {
		echo "False";
		//echo mysqli_error($con);
	} 
	else {
	
		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','AP CREDIT MEMO','$compname','Inserted New Record')");

		echo $cSINo;
	}
	
}else{
	
	$cSINo = $_REQUEST['hdtsavetyp'];
	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$nGross = $_REQUEST['txtnamt'];
	
	if(isset($_REQUEST['chkwithref'])){
		$cref = 1;
	}
		else{
			$cref = 0;
			
		}

	$crefno = $_REQUEST['txtcrefno'];

	//$ctype="Credit";
	$preparedby = $_SESSION['employeeid'];
	


	if (!mysqli_query($con, "Update apcm set `ccode` = '$cCustID',  `cremarks` = $cRemarks, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `crefno` = '$crefno',`cwithref` = $cref where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		echo "False";
		//echo mysqli_error($con);
	} 
	else {
	
		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'UPDATED','AP CREDIT MEMO','$compname','Update Record')");

		echo $cSINo;
	}
	
	
}


?>