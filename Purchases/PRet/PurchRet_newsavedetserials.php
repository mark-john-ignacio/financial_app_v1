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
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$dexpz = $_REQUEST['dneed'];	
		$locaid = $_REQUEST['clocas'];
		$serialx = $_REQUEST['seiraln'];
		$crr = $_REQUEST['xcref'];
		$nRefIdent = $_REQUEST['crefidnt'];	

		$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO purchreturn_t_serials (`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `cunit`, `nfactor`, `cmainunit`, `cserial`, `nlocation`, `dexpired`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$crr', '$nRefIdent', '$cItemNo', '$nQty', '$cUnit', 1, '$cUnit', '$serialx', '$locaid', STR_TO_DATE('$dexpz', '%m/%d/%Y'))")){
		echo "False";
		
	}
	else{
		echo "True";
	}

?>
