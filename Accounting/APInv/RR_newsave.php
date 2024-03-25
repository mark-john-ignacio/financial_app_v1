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

	$chkSales = mysqli_query($con,"select * from suppinv where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "RI".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		//echo $lastSI."<br>";
		//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "RI".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "RI".$dmonth.$dyear.$baseno;
		}
	}

	
	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_received'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 

	$cCustSI = $_REQUEST['txtSuppSI'];

	$cRefRR = $_REQUEST['txtrefrr'];

	$CurrCode = $_REQUEST['selbasecurr'];
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	
	$nnetvat = $_REQUEST['txtnNetVAT']; //VATABLE SALES   nnet
	$nexempt = $_REQUEST['txtnExemptVAT']; //VAT EXEMPT SALES   nexempt
	$nvat = $_REQUEST['txtnVAT']; //VAT   nvat
	$nGrossBefore = $_REQUEST['txtnGrossBef']; //TOTAL GROSS  BEFORE DISCOUNT ngrossbefore
	if(isset($_REQUEST['txtnEWT'])){
		$nLessEWT = $_REQUEST['txtnEWT']; //EWT
	}else{
		$nLessEWT = ""; //EWT
	}
	$nGross = $_REQUEST['txtnGross']; //TOTAL AMOUNT ngross
	$BaseGross= $_REQUEST['txtnBaseGross']; //TOTAL AMOUNT * currency rate    nbasegross

	if(isset($_REQUEST['selewt'])){
		$cewtcode = implode(",",$_REQUEST['selewt']);
	}else{
		$cewtcode = "";
	}

	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$AccntCode = $rowaccnt['cacctcode'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER	
	if (!mysqli_query($con,"INSERT INTO suppinv(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dreceived`, `nnet`, `nvat`, `nexempt`, `newt`, `cewtcode`, `ngrossbefore`, `ngross`, `nbasegross`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`, `crefsi`, `crefrr`) 
	values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nnetvat', '$nvat' ,'$nexempt', '$nLessEWT', '$cewtcode' ,'$nGrossBefore', '$nGross', '$BaseGross', '$CurrCode', '$CurrDesc', '$CurrRate', '$preparedby', 0, 0, 0, '$AccntCode','$cCustSI','$cRefRR')")){
		echo "False";
	}
	else{
	
		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','RECEIVING','$compname','Inserted New Record')");
	
	
		// Delete previous details
		mysqli_query($con, "Delete from suppinv_t Where compcode='$company' and ctranno='$cSINo'");


		echo $cSINo;
	}
	
	if(count($_FILES) != 0){
		$directory = "../../Components/assets/RI/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}

?>
