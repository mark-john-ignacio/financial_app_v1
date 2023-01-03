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
		$UCpst = $_REQUEST['ucost'];
		$nQtyFin = $_REQUEST['nqty']; //$_REQUEST['nqtyfin'];
		$cUnit = $_REQUEST['cunit'];
		//$cScan = $_REQUEST['cscan'];

		$cbcode = $_REQUEST['bcode'];
		$cserial = $_REQUEST['cserial']; //$_REQUEST['nqtyfin'];
		$selloc = $_REQUEST['selloc'];
		$dexpdte = $_REQUEST['dexpdte'];
				
		$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO invcount_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty`, `nqtyfin`, `nunitcost`, `cbarcode`, `cserial`, `nlocation`, `dexpdte`) values('$company', '$cSINo', '$refcidenttran', '$indexz', '$cItemNo', '$cUnit', '$nQty', '$nQtyFin', '$UCpst', '$cbcode', '$cserial', '$selloc', STR_TO_DATE('$dexpdte', '%m/%d/%Y'))")){
		//echo "False";
		//echo "INSERT INTO invcount_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cunit`, `nqty`, `nqtyfin`, `nunitcost`, `cbarcode`, `cserial`, `nlocation`, `dexpdte`) values('$company', '$cSINo', '$refcidenttran', '$indexz', '$cItemNo', '$cUnit', '$nQty', '$nQtyFin', '$UCpst', $cbcode', '$cserial', '$selloc', STR_TO_DATE('$dexpdte', '%m/%d/%Y'))";
		echo "Errormessage: %s\n", mysqli_error($con);
	}
	else{
		echo "True";
		
	}
	


?>
