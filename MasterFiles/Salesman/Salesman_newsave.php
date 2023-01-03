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
	$company = $_SESSION['companyid'];
	$cCustCode = strtoupper($_REQUEST['txtccode']);	
	$cCustName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);
	$ContactNo = chkgrp($_REQUEST['txtcontact']);
	$emailadd = chkgrp($_REQUEST['txtcEmail']);
		
	
	//INSERT NEW ITEM
	if(!mysqli_query($con,"INSERT INTO `salesman`(`compcode`, `ccode`, `cname`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `ctelno`, `cemailadd`, `cstatus`) VALUES ('$company', '$cCustCode', '$cCustName',  $HouseNo, $City, $State, $Country, $ZIP, $ContactNo, $emailadd, 'ACTIVE')")){
		if(mysqli_error($con)!=""){
			//echo "INSERT INTO `salesman`(`compcode`, `ccode`, `cname`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `ctelno`, `cemailadd`, `cstatus`) VALUES ('$company', '$cCustCode', '$cCustName',  $HouseNo, $City, $State, $Country, $ZIP, $ContactNo, $emailadd, 'ACTIVE')";
			printf("Errormessage: %s\n", mysqli_error($con));	
		}
	}
	

	//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'INSERTED','SALESMAN','$compname','Insert New Salesman')");


	echo "True";
?>
