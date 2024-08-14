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

	$cSINo = $_POST['txtcsalesno'];
	$cCustID = $_POST['txtcustid'];
	$dDelDate = $_POST['date_delivery'];
	$cRemarks = chkgrp($_POST['txtremarks']); 
	//$selreinv = $_POST['selreinv'];
	$selsitypz = $_POST['selsityp'];	
	$selsiseries = chkgrp($_POST['csiprintno']);
	$CurrCode = $_POST['selbasecurr']; 
	$CurrDesc = $_POST['hidcurrvaldesc'];  
	$CurrRate= $_POST['basecurrval']; 
	
	$nnetvat = $_POST['txtnNetVAT']; //VATABLE SALES   nnet
	$nexempt = $_POST['txtnExemptVAT']; //VAT EXEMPT SALES   nexempt
	$nzeror = $_POST['txtnZeroVAT']; // ZERO RATED SALES  nzerorated
	$nvat = $_POST['txtnVAT']; //VAT   nvat
	$nGrossBefore = $_POST['txtnGrossBef']; //TOTAL GROSS  BEFORE DISCOUNT ngrossbefore
	$nLessEWT = $_POST['txtnEWT']; //EWT
	$nGrossDisc = str_replace(",","",$_POST['txtnGrossDisc']);  //GROSS DISCOUNT  ngrossdisc
	$nGross = $_POST['txtnGross']; //TOTAL AMOUNT ngross
	$BaseGross= $_POST['txtnBaseGross']; //TOTAL AMOUNT * currency rate    nbasegross

	$RefMods= $_POST['txtrefmod']; 
	$RefModsNo= $_POST['txtrefmodnos']; 

	if(isset($_POST['selewt'])){
		$cewtcode = implode(",",$_POST['selewt']);
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