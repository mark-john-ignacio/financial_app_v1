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


$chkSales = mysqli_query($con,"select * from ntdr where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "DN".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "DN".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "DN".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']); 
	$nGross = $_REQUEST['ngross'];
	$nDRPrintNo = chkgrp($_REQUEST['cdrprintno']);
	$salesman = $_REQUEST['salesman'];
	$delcodes = $_REQUEST['delcodes'];
	$delhousno = $_REQUEST['delhousno'];
	$delcity = $_REQUEST['delcity'];
	$delstate = $_REQUEST['delstate'];
	$delcountry = $_REQUEST['delcountry'];
	$delzip = $_REQUEST['delzip'];

	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";

				$sqlhead = mysqli_query($con,"Select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'");
				if (mysqli_num_rows($sqlhead)!=0) {
					$row = mysqli_fetch_assoc($sqlhead);
					$cacctcode = "'".$row["cacctcodesales"]."'";
				}
	
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO ntdr(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `cacctcode`, `cdrprintno`, `csalesman`, `cdelcode`, `cdeladdno`, `cdeladdcity`, `cdeladdstate`, `cdeladdcountry`, `cdeladdzip`) 
	values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', $cacctcode, $nDRPrintNo, '$salesman', '$delcodes', '$delhousno', '$delcity', '$delstate', '$delcountry', '$delzip')")) {
		echo "False";
		//echo mysqli_error($con);
	} 
	else {

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','DR NON-TRADE','$compname','Inserted New Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from ntdr_t Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from ntdr_t_info Where compcode='$company' and ctranno='$cSINo'");

		echo $cSINo;
	}
	
	


?>