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

function chkgrp0($valz) {
	if($valz==''){
		return "0";
	}else{
    	return "'".$valz."'";
	}
}


	$company = $_SESSION['companyid'];
	$CustID = $_REQUEST['ccode'];
	
		$cSINo = $_REQUEST['trancode'];
		$crefno = chkgrp($_REQUEST['crefno']); 
		$crefident = chkgrp0($_REQUEST['crefident']); 
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice']; 
		$nDiscount = $_REQUEST['ndiscount'];
		$nAmount = $_REQUEST['namt'];
		$nTranAmount = $_REQUEST['ntranamt'];

		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];
		$cacctcode = "NULL";
	
		
				$sqlhead = mysqli_query($con,"Select A.cacctcodesales from items A where A.compcode='$company' and A.cpartno='$cItemNo'");
				
				if (mysqli_num_rows($sqlhead)!=0) {
					$row = mysqli_fetch_assoc($sqlhead);
					$cacctcode = "'".$row["cacctcodesales"]."'";
				}

	$refcidenttran = $cSINo."P".$indexz;

	if (!mysqli_query($con,"INSERT INTO ntsales_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nrefident`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `ndiscount`, `nbaseamount`, `namount`, `cmainunit`,`nfactor`,`cacctcode`) values('$company', '$refcidenttran', '$cSINo', $crefno, $crefident, '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nDiscount', '$nTranAmount', '$nAmount', '$cMainUOM', '$nFactor', $cacctcode)")){
		//echo "False";
		
		echo "Errormessage: %s\n", mysqli_error($con);
	}
	else{
		echo "True";
	}
	


?>
