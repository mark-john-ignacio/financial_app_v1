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
		$cUnit = $_REQUEST['cuom'];
		$nRefIdent = $_REQUEST['crefidnt'];	
		$dexpz = $_REQUEST['dneed'];	
		$locaid = $_REQUEST['clocas'];
		$crr = $_REQUEST['xcref'];
		$serialx = $_REQUEST['seiraln'];
		$putref = $_REQUEST['putref'];
		$putrefident = $_REQUEST['putrefident'];

		$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO so_pick_t(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `cunit`, `nfactor`, `cmainunit`, `cserial`, `nlocation`, `dexpired`, `crefput`, `crefputident`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$crr', '$nRefIdent', '$cItemNo', '$nQty', '$cUnit', 1, '$cUnit', '$serialx', '$locaid', STR_TO_DATE('$dexpz', '%m/%d/%Y'), '$putref', '$putrefident')")){
		echo "False";
		
	}
	else{
		echo "True";
	}

?>
