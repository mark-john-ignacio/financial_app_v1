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
		$nrefident = $_REQUEST['ident'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$nAmount = $_REQUEST['namt'];
		$nDiscount = $_REQUEST['ndiscount'];
		$nTranAmount = $_REQUEST['ntranamt'];
		$nOrigQty = $_REQUEST['nqtyorig'];
		
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];
		
		$creason = chkgrp($_REQUEST['creason']);
		
		$refcidenttran = $cSINo."P".$indexz;

	if (!mysqli_query($con,"INSERT INTO salesreturn_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nident`, `nrefident`,`citemno`, `nqty`, `norigqty`, `cunit`, `nprice`, `ndiscount`, `nbaseamount`, `namount`, `cmainunit`,`nfactor`,`creason`) values('$company', '$refcidenttran', '$cSINo', $crefno, '$indexz', '$nrefident','$cItemNo', '$nQty', '$nOrigQty', '$cUnit', '$nPrice', '$nDiscount', '$nTranAmount', '$nAmount', '$cMainUOM', $nFactor, $creason)")){
		//echo "False";
		
		echo "Errormessage: %s\n", mysqli_error($con);
	}
	else{
		echo "True";
		
	}
	


?>
