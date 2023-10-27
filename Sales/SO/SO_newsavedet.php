<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		$crefno = chkgrp($_REQUEST['crefno']); 
		$nrefident = $_REQUEST['nrefident'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$nAmount = $_REQUEST['namt'];
		$nBaseAmount = $_REQUEST['nbaseamt'];
		
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];

		$ctaxcode = $_REQUEST["vatcode"]; 
		$nrate = $_REQUEST["nrate"]; 

		$citmrmx = chkgrp($_REQUEST["citmremx"]); 
		
		$refcidenttran = $cSINo."P".$indexz;
	

	//echo "INSERT INTO so_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nrefident`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `nbaseamount`, `cmainunit`,`nfactor`,`ctaxcode`, `nrate`) values('$company', '$refcidenttran', '$cSINo', $crefno, '$nrefident', '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nAmount', '$nBaseAmount', '$cMainUOM', $nFactor, '$ctaxcode', '$nrate')";

	if (!mysqli_query($con,"INSERT INTO so_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nrefident`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `nbaseamount`, `cmainunit`,`nfactor`,`ctaxcode`, `nrate`, `citemremarks`) values('$company', '$refcidenttran', '$cSINo', $crefno, '$nrefident', '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nAmount', '$nBaseAmount', '$cMainUOM', $nFactor, '$ctaxcode', '$nrate', $citmrmx)")){
		echo "False";
	}
	else{
		echo "True";
	}
	


?>
