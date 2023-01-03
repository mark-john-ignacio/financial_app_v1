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


$chkSales = mysqli_query($con,"select * from aradj where compcode='$company' and ctype='Credit' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "CM".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "CM".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "CM".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']); 
	$nGross = $_REQUEST['ngross'];
	$ctype="Credit";
	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER

				$sqlhead = mysqli_query($con,"Select cacctcodesales, cvattype from customers where compcode='$company' and cempid='$cCustID'");
				if (mysqli_num_rows($sqlhead)!=0) {
					$row = mysqli_fetch_assoc($sqlhead);
					$cacctcode = "'".$row["cacctcodesales"]."'";
					$cvatcode = "'".$row["cvattype"]."'";
				}


	if (!mysqli_query($con, "INSERT INTO aradj(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `ctype`, `cpreparedby`, `cacctcode`, `cvatcode`) values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$ctype', '$preparedby', $cacctcode, $cvatcode)")) {
		echo "False";
		//echo mysqli_error($con);
	} 
	else {
	
		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','CREDIT MEMO','$compname','Inserted New Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from aradj_t Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from aradj_t_info Where compcode='$company' and ctranno='$cSINo'");

		echo $cSINo;
	}
	
	


?>