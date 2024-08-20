<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');
require_once('../../Model/helper.php');

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


$chkSales = mysqli_query($con,"select * from salesreturn where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ddate desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SR".$dyear."000000001";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dyear){
		$cSINo = "SR".$dyear."000000001";
	}
	else{
		$baseno = intval(substr($lastSI,4,9)) + 1;
		$zeros = 9 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SR".$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	
	/*
		$nGross = str_replace(",","",$_REQUEST['txtnGross']);
		$CurrCode = $_REQUEST['selbasecurr']; 
		$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
		$CurrRate= $_REQUEST['basecurrval']; 
		$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);
		
		, `ngross`, `nbasegross`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`
		, '$nGross', '$BaseGross', '$CurrCode', '$CurrDesc', '$CurrRate'

	*/

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO salesreturn(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dreceived`, `cpreparedby`) values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$preparedby')")) {
		echo "False";
		//echo mysqli_error($con);
	} 
	else {
		echo $cSINo;

		//INSERT LOGFILE
		$compname = php_uname('n');
	
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','SALES RETURN','$compname','Inserted New Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from salesreturn_t Where compcode='$company' and ctranno='$cSINo'");
	}
	

	//mysqli_query($con, "Delete from salesreturn_t_info Where compcode='$company' and ctranno='$cSINo'");
	if(count($_FILES) != 0){
		$directory = "../../Components/assets/SR/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}
	
?>