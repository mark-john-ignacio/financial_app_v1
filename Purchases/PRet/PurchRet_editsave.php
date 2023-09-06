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



$cSINo = $_REQUEST['pono'];
$company = $_SESSION['companyid'];

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
	
	//UPDATE HEADER

	if (!mysqli_query($con,"Update purchreturn set `ccode` ='$cCustID', `cremarks`=$cRemarks, `dreturned`=STR_TO_DATE('$dDelDate', '%m/%d/%Y'),`ngross`='$nGross', `ccustacctcode`='$AccntCode' Where compcode='$company' and ctranno='$cSINo'")){
		echo "False";
	}
	else{
		echo $cSINo;
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cSINo','$preparedby',NOW(),'UPDATED','PURCHASE RETURN','$compname','Updated Record')");

	// Delete previous details
	mysqli_query($con, "Delete from purchreturn_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from purchreturn_t_serials Where compcode='$company' and ctranno='$cSINo'");	

	if(count($_FILES) != 0){
		$directory = "../../Components/assets/PR/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		upload_image($_FILES, $directory);
	}

?>
