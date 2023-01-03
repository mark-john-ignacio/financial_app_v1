<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');


	$company = $_SESSION['companyid'];
	
		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$nQtyFin = $_REQUEST['nqty']; //$_REQUEST['nqtyfin'];
		$cUnit = $_REQUEST['cunit'];
		//$cScan = $_REQUEST['cscan'];

		$cbcode = $_REQUEST['bcode'];
		$cserial = $_REQUEST['cserial']; //$_REQUEST['nqtyfin'];
		$selloc = $_REQUEST['selloc'];
		$dexpdte = $_REQUEST['dexpdte'];
				
		$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO invcount_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty`, `nqtyfin`, `cbarcode`, `cserial`, `nlocation`, `dexpdte`) values('$company', '$cSINo', '$refcidenttran', '$indexz', '$cItemNo', '$cUnit', '$nQty', '$nQtyFin', '$cbcode', '$cserial', '$selloc', '$dexpdte')")){
		//echo "False";
		//echo "INSERT INTO invcount_t(`compcode`, `cidentity`, `ctranno`, `nidentity`, `citemno`, `nqty`, `cunit`, `nfactor`, `cmainunit`, `cserial`, `cbarcode`, `nlocation`, `dexpired`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cItemNo', '$nQty', '$cUnit', 1, '$cUnit', '$cserial', '$cbcode', '$selloc', '$dexpdte')";
		echo "Errormessage: %s\n", mysqli_error($con);
	}
	else{
		echo "True";
		
	}
	


?>
