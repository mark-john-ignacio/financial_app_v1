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
	$locaid = $_REQUEST['clocas'];
	$xlotsno = $_REQUEST['xlotsno'];
	$xpckslist = $_REQUEST['xpckslist'];
	$crr = $_REQUEST['xcref'];
	$crrid = $_REQUEST['xcrefid'];
	$nRefIdent = $_REQUEST['crefidnt'];	

	$refcidenttran = $cSINo."P".$indexz;

	if (!mysqli_query($con,"INSERT INTO purchreturn_t_serials (`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `citemno_nident`, `nqty`, `cunit`, `nfactor`, `cmainunit`, `clotsno`, `cpacklist`, `nlocation`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$crr', '$crrid', '$cItemNo', '$nRefIdent', '$nQty', '$cUnit', 1, '$cUnit', '$xlotsno', '$xpckslist', '$locaid')")){
		echo "False";		
	}
	else{
		echo "True";
	}

?>
