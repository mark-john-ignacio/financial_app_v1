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

	$company = $_SESSION['companyid'];

	$cSINo = $_REQUEST['txtcsalesno'];
	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	//$selreinv = $_REQUEST['selreinv'];
	$selsitypz = $_REQUEST['selsityp'];	
	$selsiseries = chkgrp($_REQUEST['csiprintno']);
	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	
	$nnetvat = $_REQUEST['txtnNetVAT']; //VATABLE SALES   nnet
	$nexempt = $_REQUEST['txtnExemptVAT']; //VAT EXEMPT SALES   nexempt
	$nzeror = $_REQUEST['txtnZeroVAT']; // ZERO RATED SALES  nzerorated
	$nvat = $_REQUEST['txtnVAT']; //VAT   nvat
	$nGrossBefore = $_REQUEST['txtnGrossBef']; //TOTAL GROSS  BEFORE DISCOUNT ngrossbefore
	$nLessEWT = $_REQUEST['txtnEWT']; //EWT
	$nGrossDisc = str_replace(",","",$_REQUEST['txtnGrossDisc']);  //GROSS DISCOUNT  ngrossdisc
	$nGross = $_REQUEST['txtnGross']; //TOTAL AMOUNT ngross
	$BaseGross= $_REQUEST['txtnBaseGross']; //TOTAL AMOUNT * currency rate    nbasegross

	$RefMods= $_REQUEST['txtrefmod']; 
	$RefModsNo= $_REQUEST['txtrefmodnos']; 

	if(isset($_REQUEST['selewt'])){
		$cewtcode = implode(",",$_REQUEST['selewt']);
	}else{
		$cewtcode = "";
	}
	
	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";
	$cvatcode = "NULL";

	$sqlhead = mysqli_query($con,"Select cacctcodesales, cvattype from customers where compcode='$company' and cempid='$cCustID'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cacctcode = "'".$row["cacctcodesales"]."'";
		$cvatcode = "'".$row["cvattype"]."'";
	}


	if (!mysqli_query($con, "UPDATE sales set `ccode` = '$cCustID', `cremarks` = $cRemarks, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `nnet` = '$nnetvat', `nvat` = '$nvat', `cacctcode` = $cacctcode, `cvatcode` = $cvatcode, `lapproved` = 0, `csalestype` = '$selsitypz', `csiprintno` = $selsiseries, `nbasegross` = '$BaseGross', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `crefmodule` = '$RefMods', `crefmoduletran` = '$RefModsNo', `cewtcode` = '$cewtcode', `nexempt` = '$nexempt', `nzerorated` = '$nzeror', `ngrossbefore` = '$nGrossBefore', `ngrossdisc` = '$nGrossDisc', `newt` = '$nLessEWT' where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		echo "False";

		
	} 
	else {
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
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','SALES INVOICE','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from sales_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from sales_t_info Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from sales_t_disc Where compcode='$company' and ctranno='$cSINo'");

?>