<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}

		$cSINo = $_REQUEST['trancode'];
		$dneed = $_REQUEST['dneed'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$nBaseAmount = $_REQUEST['ntranamt'];
		$nAmount = $_REQUEST['namt']; 
		$cRemarks = chkgrp(mysqli_real_escape_string($con,$_REQUEST['citmremarks']));
		
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];

		$chkItmAcct = mysqli_query($con,"select cacctcodewrr from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodewrr from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodewrr'];
	
		}
		
		if($cItemNo=="NEW_ITEM"){
			$cItemDesc = "'".$_REQUEST['citmdesc']."'";
		}else{
			$cItemDesc = "NULL";
		}
		
	$refcidenttran = $cSINo."P".$indexz;

	if (!mysqli_query($con,"INSERT INTO purchase_t(`compcode`, `cidentity`, `cpono`, `nident`, `citemno`, `citemdesc`, `nqty`, `cunit`, `nprice`, `nbaseamount`, `namount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`, `ddateneeded`, `cremarks`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cItemNo',  $cItemDesc, '$nQty', '$cUnit', '$nPrice', '$nBaseAmount', '$nAmount', 0, $nFactor, '$cMainUOM', '$ItmAccnt', STR_TO_DATE('$dneed', '%m/%d/%Y'), $cRemarks)")){
		echo "False";
	}
	else{
		echo "True";
	}

?>
