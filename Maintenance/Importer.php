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
	$CustTyp = $_REQUEST['seltyp'];
	$CustCls = $_REQUEST['selcls'];
	$CreditLimit = $_REQUEST['txtclimit'];
	$PriceVer = $_REQUEST['selpricever']; 
	$VatType = $_REQUEST['selvattype']; 
	$Terms = $_REQUEST['selcterms']; 
	
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
	
	$cGrp1 = chkgrp($_REQUEST['txtCustGroup1D']);
	$cGrp2 = chkgrp($_REQUEST['txtCustGroup2D']);
	$cGrp3 = chkgrp($_REQUEST['txtCustGroup3D']);
	$cGrp4 = chkgrp($_REQUEST['txtCustGroup4D']);
	$cGrp5 = chkgrp($_REQUEST['txtCustGroup5D']);
	$cGrp6 = chkgrp($_REQUEST['txtCustGroup6D']);
	$cGrp7 = chkgrp($_REQUEST['txtCustGroup7D']);
	$cGrp8 = chkgrp($_REQUEST['txtCustGroup8D']);
	$cGrp9 = chkgrp($_REQUEST['txtCustGroup9D']);
	$cGrp10 = chkgrp($_REQUEST['txtCustGroup10D']);

	$cparent = chkgrp($_REQUEST['txtcparentD']); 

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT NEW ITEM
	if(!mysqli_query($con,"INSERT INTO `customers`(`compcode`, `cempid`, `cname`, `cacctcodesales`,`ccustomertype`, `ccustomerclass`, `cpricever`, `cvattype`, `cterms`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `cphone`, `cmobile`, `ccontactname`, `cemail`, `cdesignation`, `cstatus`, `nlimit`, `cparentcode`) VALUES ('$company', '$cCustCode', '$cCustName', '$SalesCode','$CustTyp', '$CustCls', '$PriceVer', '$VatType', '$Terms', $HouseNo, $City, $State, $Country, $ZIP, $PhoneNo, $Mobile, $Contact, $Email, $Desig, 'ACTIVE', '$CreditLimit', $cparent)")){
		if(mysqli_error($con)!=""){
			echo "Errormessage: ".mysqli_error($con)."<br>" ;	
		}
	}

?>
