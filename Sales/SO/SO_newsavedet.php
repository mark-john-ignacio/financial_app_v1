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
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$nAmount = $_REQUEST['namt'];
		$nBaseAmount = $_REQUEST['nbaseamt'];
		
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];

	$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO so_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `nbaseamount`, `cmainunit`,`nfactor`) values('$company', '$refcidenttran', '$cSINo', $crefno, '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nAmount', '$nBaseAmount', '$cMainUOM', $nFactor)")){
		echo "False";
	}
	else{
		echo "True";
	}
	


?>
