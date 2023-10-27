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
		return "''";
	}else{
		return "'".str_replace("'","\'",$valz)."'";
	}
}


$chkSales = mysqli_query($con,"select * from so where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SO".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SO".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SO".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery']; 
	$dPODate = $_REQUEST['date_PO']; 
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$nGross = str_replace(",","",$_REQUEST['txtnGross']);
	$cSITyp = $_REQUEST['selsityp'];
	$cCPONO = $_REQUEST['txtcPONo'];
	$delcodes = $_REQUEST['txtdelcustid'];
	$delhousno = chkgrp($_REQUEST['txtchouseno']);
	$delcity = chkgrp($_REQUEST['txtcCity']);
	$delstate = chkgrp($_REQUEST['txtcState']);
	$delcountry = chkgrp($_REQUEST['txtcCountry']);
	$delzip = $_REQUEST['txtcZip'];
	$specins = chkgrp($_REQUEST['txtSpecIns']);

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);

	if(isset($_REQUEST['txtsalesmanid'])){
		$salesman = $_REQUEST['txtsalesmanid'];
	}else{
		$salesman = "";
	}
	
	$preparedby = $_SESSION['employeeid'];
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO so(`compcode`, `ctranno`, `ccode`, `cremarks`, `cspecins`, `ddate`, `dcutdate`, `dpodate`, `ngross`, `nbasegross`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`, `cpreparedby`, `csalestype`, `cpono`, `csalesman`, `cdelcode`, `cdeladdno`, `cdeladdcity`, `cdeladdstate`, `cdeladdcountry`, `cdeladdzip`) 
	values('$company', '$cSINo', '$cCustID', $cRemarks, $specins, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), STR_TO_DATE('$dPODate', '%m/%d/%Y'), '$nGross', '$BaseGross', '$CurrCode', '$CurrDesc', '$CurrRate', '$preparedby', '$cSITyp', '$cCPONO', '$salesman', '$delcodes', $delhousno, $delcity, $delstate, $delcountry, '$delzip')")) {
		
		echo "False";
		//echo mysqli_error($con);
	} 
	else {

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','SALES ORDER','$compname','Inserted New Record')");
		
		// Delete previous details
		mysqli_query($con, "Delete from so_t Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from so_t_info Where compcode='$company' and ctranno='$cSINo'");

		echo $cSINo;

		//print_r($_FILES);
	}

	if(count($_FILES) != 0){
		$directory = "../../Components/assets/SO/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}
?>  