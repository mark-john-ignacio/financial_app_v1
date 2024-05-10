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


	$chkSales = mysqli_query($con,"select * from purchreturn where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ddate desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "PT".$dyear."000000001";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		//echo $lastSI."<br>";
		//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
		if(substr($lastSI,2,2) <> $dyear){
			$cSINo = "PT".$dyear."000000001";
		}
		else{
			$baseno = intval(substr($lastSI,4,9)) + 1;
			$zeros = 9 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "PT".$dmonth.$dyear.$baseno;
		}
	}

	
	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']); 
	$nGross = $_REQUEST['ngross'];
	
	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$AccntCode = $rowaccnt['cacctcode'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER	
	if (!mysqli_query($con,"INSERT INTO purchreturn(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dreturned`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`) 
	values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode')")){
		echo "False";
	}
	else{

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE RETURN','$compname','Inserted New Record')");
	
	
		// Delete previous details
		mysqli_query($con, "Delete from purchreturn_t Where compcode='$company' and ctranno='$cSINo'");

		echo $cSINo;
	}


	if(count($_FILES) != 0){
		$directory = "../../Components/assets/PR/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}
	


?>
