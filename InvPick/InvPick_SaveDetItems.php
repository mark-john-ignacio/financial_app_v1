<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['itmcd'];
		$nQty = $_REQUEST['qtycd'];

		$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO so_pick_items(`compcode`, `cidentity`, `ctranno`, `nident`, `citemno`, `ntotqty`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cItemNo', '$nQty')")){
		echo "False"."INSERT INTO so_pick_items(`compcode`, `cidentity`, `ctranno`, `nident`, `citemno`, `ntotqty`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cItemNo', '$nQty')";
		
	}
	else{
		echo "True";
	}

?>
