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

////echo "<pre>";
////print_r($_POST);
////echo "</pre>";


$chkSales = mysqli_query($con,"select * from sales where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SI".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SI".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SI".$dmonth.$dyear.$baseno;
	}
}

	$cCustID = $_POST['txtcustid'];
	$dDelDate = $_POST['date_delivery'];
	$cRemarks = chkgrp($_POST['txtremarks']); 
	
	//$selreinv = $_POST['selreinv'];	
	$selsitypz = $_POST['selsityp']; 
	$selpaytyp = $_POST['selpaytyp']; 
	$selsiseries = chkgrp($_POST['csiprintno']);  
	$cterms = $_POST['selcterms'];
	$CurrCode = $_POST['selbasecurr']; 
	$CurrDesc = $_POST['hidcurrvaldesc'];  
	$CurrRate= $_POST['basecurrval'];

	$nnetvat = $_POST['txtnNetVAT']; //VATABLE SALES   nnet
	$nexempt = $_POST['txtnExemptVAT']; //VAT EXEMPT SALES   nexempt
	$nzeror = $_POST['txtnZeroVAT']; // ZERO RATED SALES  nzerorated
	$nvat = $_POST['txtnVAT']; //VAT   nvat
	$nGrossBefore = $_POST['txtnGrossBef']; //TOTAL GROSS  BEFORE DISCOUNT ngrossbefore

	if(isset($_POST['txtnEWT'])){
		$nLessEWT = $_POST['txtnEWT']; //EWT
	}else{
		$nLessEWT = ""; //EWT
	}
	
	$nGrossDisc = str_replace(",","",$_POST['txtnGrossDisc']);  //GROSS DISCOUNT  ngrossdisc
	$nGross = $_POST['txtnGross']; //TOTAL AMOUNT ngross
	$BaseGross= $_POST['txtnBaseGross']; //TOTAL AMOUNT * currency rate    nbasegross

	$cewtcode = "";
	if(isset($_POST['selewt'])){
		if($_POST['selewt']!=""){
			$cewtcode = implode(",",$_POST['selewt']);
		}
	}
	
	$RefMods= $_POST['txtrefmod']; 
	$RefModsNo= $_POST['txtrefmodnos']; 
	
	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";
	$cvatcode = "NULL";

	$sqlhead = mysqli_query($con,"Select cacctcodesales, cvattype,cterms from customers where compcode='$company' and cempid='$cCustID'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cacctcode = "'".$row["cacctcodesales"]."'";
		$cvatcode = "'".$row["cvattype"]."'";
		//$cterms = "'".$row["cterms"]."'";
	}
	
	//INSERT HEADER

	if (!mysqli_query($con, "INSERT INTO sales(`compcode`, `ctranno`, `ccode`, `cterms`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `nbasegross`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`, `nnet`, `nvat`, `cpreparedby`, `cacctcode`, `cvatcode`, `csalestype`, `cpaytype`, `csiprintno`, `crefmodule`, `crefmoduletran`, `cewtcode`, `nexempt`, `nzerorated`, `ngrossbefore`, `ngrossdisc`, `newt`) values('$company', '$cSINo', '$cCustID', '$cterms', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$BaseGross', '$CurrCode', '$CurrDesc', '$CurrRate', '$nnetvat', '$nvat', '$preparedby', $cacctcode, $cvatcode, '$selsitypz', '$selpaytyp', $selsiseries, '$RefMods', '$RefModsNo', '$cewtcode', '$nexempt', '$nzeror', '$nGrossBefore', '$nGrossDisc', '$nLessEWT')")) {
		echo "False";
		echo mysqli_error($con);
	} 
	else {
		
		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `module`, `cevent`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'SALES INVOICE','INSERTED','$compname','Inserted New Record')");

		// Delete previous details
		mysqli_query($con, "Delete from sales_t Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from sales_t_info Where compcode='$company' and ctranno='$cSINo'");
		mysqli_query($con, "Delete from sales_t_disc Where compcode='$company' and ctranno='$cSINo'");
		
		echo $cSINo;
	}
	
	if(count($_FILES) != 0){
		$directory = "../../Components/assets/SI/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}

?>