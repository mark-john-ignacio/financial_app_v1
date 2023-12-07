<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');
require_once ('../../Model/helper.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "''";
	}else{
    return "'".str_replace("'","\'",$valz)."'";
	}
}


$chkSales = mysqli_query($con,"select * from dr where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "DR".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "DR".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "DR".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']); 
	$nGross = str_replace(",","",$_REQUEST['ngross']);
	$nDRPrintNo = chkgrp($_REQUEST['cdrprintno']);
	$salesman = $_REQUEST['salesman'];
	$delcodes = $_REQUEST['delcodes'];
	$delhousno = chkgrp($_REQUEST['delhousno']);
	$delcity = chkgrp($_REQUEST['delcity']);
	$delstate = chkgrp($_REQUEST['delstate']);
	$delcountry = chkgrp($_REQUEST['delcountry']);
	$delzip = $_REQUEST['delzip'];

	$cdrapcord = chkgrp($_REQUEST['cdrapcord']);
	$cdrapcdr = chkgrp($_REQUEST['cdrapcdr']);

	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";

	$sqlhead = mysqli_query($con,"Select cacctcodesales,cterms from customers where compcode='$company' and cempid='$cCustID'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cacctcode = "'".$row["cacctcodesales"]."'";
		$cterms = "'".$row["cterms"]."'";
	}
	
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO dr(`compcode`, `ctranno`, `ccode`, `cterms`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `cacctcode`, `cdrprintno`, `csalesman`, `cdelcode`, `cdeladdno`, `cdeladdcity`, `cdeladdstate`, `cdeladdcountry`, `cdeladdzip`, `crefapcord`, `crefapcdr`) 
	values('$company', '$cSINo', '$cCustID', $cterms, $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', $cacctcode, $nDRPrintNo, '$salesman', '$delcodes', $delhousno, $delcity, $delstate, $delcountry, '$delzip', $cdrapcord, $cdrapcdr)")) {
		echo "False";

		echo mysqli_error($con);
	} 
	else {

		$txtpullrqs = chkgrp($_REQUEST['txtpullrqs']);
		$txtpullrmrks = chkgrp($_REQUEST['txtpullrmrks']);
		$txtRevNo = chkgrp($_REQUEST['txtRevNo']);
		$txtSalesRep = chkgrp($_REQUEST['txtSalesRep']);
		$txtTruckNo = chkgrp($_REQUEST['txtTruckNo']);
		$txtDelSch = chkgrp($_REQUEST['txtDelSch']);
		$txtRevOthers = chkgrp($_REQUEST['txtRevOthers']);
		$DRfootCert = chkgrp($_REQUEST['DRfootCert']);
		$DRfootIssu = chkgrp($_REQUEST['DRfootIssu']);
		$DRfootChec = chkgrp($_REQUEST['DRfootChec']);
		$DRfootAppr = chkgrp($_REQUEST['DRfootAppr']);

		//INSERT APCDR DETAILS
		mysqli_query($con,"INSERT INTO dr_apc_t(`compcode`, `ctranno`, `cpull_advice`, `cpull_remarks`, `crevno`, `csalesrep`, `ctruckno`, `cdelsched`, `cothers`, `ccertified`, `cissued`, `cchecked`, `capproved`) 
		values('$company','$cSINo',$txtpullrqs,$txtpullrmrks,$txtRevNo,$txtSalesRep,$txtTruckNo,$txtDelSch,$txtRevOthers,$DRfootCert,$DRfootIssu,$DRfootChec,$DRfootAppr)");

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','DELIVERY RECEIPT','$compname','Inserted New Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from dr_t Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from dr_t_info Where compcode='$company' and ctranno='$cSINo'");

		echo $cSINo;
	}
	
	if(count($_FILES) != 0){
		$directory = "../../Components/assets/DR/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}
?>