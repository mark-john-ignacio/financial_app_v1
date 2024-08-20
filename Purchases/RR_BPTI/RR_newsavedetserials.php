<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nRefIdent = $_REQUEST['crefidnt'];	
		$crr = $_REQUEST['xcref'];

		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
			
		$locaid = $_REQUEST['clocas'];		
		$lotsno = $_REQUEST['clotsx']; 
		$packsl = $_REQUEST['cpackl'];
		
		$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO receive_t_serials(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `cunit`, `nfactor`, `cmainunit`, `clotsno`, `cpacklist`, `nlocation`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$crr', '$nRefIdent', '$cItemNo', '$nQty', '$cUnit', 1, '$cUnit', '$lotsno', '$packsl', '$locaid')")){
		echo "False";
		
	}
	else{
		echo "True";
	}

?>
