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
	$SalesCode = $_REQUEST['txtsalesacctD'];
	$Type = $_REQUEST['seltyp'];
	$Class = $_REQUEST['selcls'];
	$Terms = $_REQUEST['selterms'];
	
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
	$sql = "INSERT INTO `suppliers`(`compcode`, `ccode`, `cname`, `cacctcode`, `cterms`, `csuppliertype`, `csupplierclass`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `cphone`, `cmobile`, `ccontactname`, `cemail`, `cdesignation`) VALUES ('$company', '$cCustCode', '$cCustName', '$SalesCode', '$Terms', '$Type', '$Class', $HouseNo, $City, $State, $Country, $ZIP, $PhoneNo, $Mobile, $Contact, $Email, $Desig)";	


	if (!mysqli_query($con, $sql)) {
		if(mysqli_error($con)!=""){
			echo "Error Main: ".mysqli_error($con);
		}
	}
					
					
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'INSERTED','SUPPLIER','$compname','Insert New Supplier')");


	echo "True";
?>