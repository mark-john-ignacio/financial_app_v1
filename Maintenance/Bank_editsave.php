<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

function chkgrp($valz) {
	global $con;
	
	if($valz==''){
		return "NULL";
	}else{
    	return "'".mysqli_real_escape_string($con, $valz)."'";
	}
}

	$cCustCode = strtoupper($_REQUEST['txtccode']);
	$company = $_SESSION['companyid'];
	
	$cCustName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	$cCashNo = chkgrp($_REQUEST['txtcoaacct']);
	$cBankNo = chkgrp($_REQUEST['txtbankacct']);
	$cBankName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txtbankacctnme']));
	
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);

	$Contact = chkgrp($_REQUEST['txtcperson']);
	$Desig = chkgrp($_REQUEST['txtcdesig']);
	$Email = chkgrp($_REQUEST['txtcEmail']);
	$PhoneNo = chkgrp($_REQUEST['txtcphone']);
	$Mobile = chkgrp($_REQUEST['txtcmobile']);
	
	$preparedby = $_SESSION['employeeid'];
	
	//INSERT NEW ITEM
	$sql = "UPDATE `bank` set `cname`='$cCustName', `cacctno`=$cCashNo, `cbankacctno`=$cBankNo, `caccountname`='$cBankName', `ccontact`=$Contact, `cdesignation`=$Desig, `cemail`=$Email, `cphoneno`=$PhoneNo, `cmobile`=$Mobile, `caddress`=$HouseNo, `ccity`=$City, `cstate`=$State, `ccountry`=$Country, `czip`=$ZIP where `compcode` = '$company' and `ccode` = '$cCustCode'";	


	if (!mysqli_query($con, $sql)) {
		if(mysqli_error($con)!=""){
			echo "Error Main: ".mysqli_error($con);
		}
	}

	//INSERT Checkbook Details
	
	mysqli_query($con, "DELETE FROM `bank_check` where `compcode` = '$company'  and `ccode` = '$cCustCode'");
	
	$ChkBkCnt = $_REQUEST['hdnchkbkcnt'];
	//INSERT FACTOR IF MERON
	if($ChkBkCnt>=1){

		for($z=1; $z<=$ChkBkCnt; $z++){
			$cCheckNo = $_REQUEST['txtchkbookno'.$z];
			$cCheckFr = $_REQUEST['txtchkfrom'.$z];
			$cCheckTo = $_REQUEST['txtcheckto'.$z];
			$cCheckNow = $_REQUEST['txtcurrentchk'.$z];
			
			$cIdentity = $cCustCode.$z;
						
			if (!mysqli_query($con, "INSERT INTO `bank_check`(`compcode`, `cidentity`, `nidentity`, `ccode`, `ccheckno`, `ccheckfrom`, `ccheckto`, `ccurrentcheck`) VALUES ('$company','$cIdentity',$z,'$cCustCode','$cCheckNo','$cCheckFr','$cCheckTo','$cCheckNow')")) {
					if(mysqli_error($con)!=""){
						$myerror =  "Error Checkbook: ".mysqli_error($con);
					}
			} 

			$cCheckNo = "";
			$cCheckFr = "";
			$cCheckTo = "";
			$cCheckNow = "";

		}
	}
					
					
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'UPDATED','BANK','$compname','Updated New Bank')");


	echo "True";
?>