<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

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
	$cChkNo = chkgrp($_REQUEST['txtchkno']);
	
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
	$sql = "INSERT INTO `bank`(`compcode`, `ccode`, `cname`, `caccntno`, `cbankacctno`, `cnxtchkno`, `ccontact`, `cdesignation`, `cemail`, `cphoneno`, `cmobile`, `caddress`, `ccity`, `cstate`, `ccountry`, `czip`, `cstatus`) VALUES ('$company', '$cCustCode', '$cCustName', $cCashNo, $cBankNo, $cChkNo, $Contact, $Desig, $Email, $PhoneNo, $Mobile, $HouseNo, $City, $State, $Country, $ZIP, 'ACTIVE')";	


	if (!mysqli_query($con, $sql)) {
		if(mysqli_error($con)!=""){
			echo "Error Main: ".mysqli_error($con);
		}
	}
					
					
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'INSERTED','BANK','$compname','Insert New Bank')");


	echo "True";
?>