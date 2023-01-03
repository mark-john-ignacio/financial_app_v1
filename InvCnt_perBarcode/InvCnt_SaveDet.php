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
		$nQtyFin = $_REQUEST['nqtyfin'];
		$cUnit = $_REQUEST['cunit'];
		$cScan = $_REQUEST['cscan'];
		
	$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO invcount_t(`compcode`, `ctranno`, `cidentity`, `nidentity`, `citemno`, `cscancode`, `cunit`, `nqty`, `nqtyfin`) values('$company', '$cSINo', '$refcidenttran', '$indexz', '$cItemNo', '$cScan', '$cUnit', '$nQty', '$nQtyFin')")){
		//echo "False";
		
		echo "Errormessage: %s\n", mysqli_error($con);
	}
	else{
		echo "True";
		
	}
	


?>
