<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		//$dneed = $_REQUEST['dneed'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		//$nPrice = $_REQUEST['nprice'];
		//$nAmount = $_REQUEST['namt'];
		//$nTranAmount = $_REQUEST['namt'];
		
		$cRef = $_REQUEST['xcref'];
		$nQtyOrig = $_REQUEST['nqtyorig'];
		$nRefIdent = $_REQUEST['crefidnt'];
		
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

		/*
		if($cUnit==$cMainUOM){
			$ncost = $nPrice;
		}
		else{
			$ncost = floatval($nPrice) / (floatval($nQty) * floatval($nFactor));
		}
		*/

		//echo floatval($nPrice) . " /  ( " . floatval($nQty) . " * " . floatval($nFactor) .")";

	$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO receive_t(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`,`nfactor`, `cmainunit`, `cacctcode`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cRef', '$nRefIdent', '$cItemNo', '$nQty', '$nQtyOrig', '$cUnit', $nFactor, '$cMainUOM', '$ItmAccnt')")){
		echo "False";
		//echo "Error:".mysqli_error($con)."<br>";
	}
	else{
		echo "True";
	}

?>
