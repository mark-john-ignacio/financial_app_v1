<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];
//$index = 0;

		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$nAmount = $_REQUEST['namt']; 
		$nBaseAmount = $_REQUEST['nbaseamt'];
		
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];

		//$index = $index + 1;

	$refcidenttran = $company.$cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO quote_t(`compcode`, `cidentity`, `ctranno`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, nbaseamount , `cmainunit`,`nfactor`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nAmount', '$nBaseAmount', '$cMainUOM', $nFactor)")){
		echo "False";
	}
	else{
		echo "True";
	}
	


?>
