<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


	$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		$crefno = chkgrp($_REQUEST['crefno']);
		$indexz = $_REQUEST['indx'];
		$nrefident = $_REQUEST['ident'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$CurrCode = $_REQUEST['currcode'];
		$CurrRate = $_REQUEST['currate'];
		$nOrigQty = $_REQUEST['nqtyorig'];
		
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];
		
		$creason = chkgrp($_REQUEST['creason']);
		
		$refcidenttran = $cSINo."P".$indexz;

		// /`nprice`, `ndiscount`, `nbaseamount`, `namount`,
		// /'$nPrice', '$nDiscount', '$nTranAmount', '$nAmount',
		$nTranAmount = floatval($nPrice) * floatval($nQty);
		$nAmount = floatval($nTranAmount) * floatval($CurrRate);

	if (!mysqli_query($con,"INSERT INTO salesreturn_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nident`, `nrefident`,`citemno`, `nqty`, `nqtyorig`, `cunit`, `cmainunit`,`nfactor`,`creason`,`nprice`,`nbaseamount`,`namount`,`ccurrcode`,`ncurrate`) values('$company', '$refcidenttran', '$cSINo', $crefno, '$indexz', '$nrefident','$cItemNo', '$nQty', '$nOrigQty', '$cUnit',  '$cMainUOM', $nFactor, $creason, '$nPrice', '$nAmount', '$nTranAmount', '$CurrCode', '$CurrRate')")){
		//echo "False";
		
		echo "Errormessage: %s\n", mysqli_error($con);

	}
	else{
		echo "True";
		
		//Update gross
		$ngross = 0;
		$nbasegross = 0;
		$chkSales = mysqli_query($con,"select * from salesreturn_t where compcode='$company' and ctranno='$cSINo'");
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$ngross = $ngross + floatval($row['namount']);
			$nbasegross = $nbasegross + floatval($row['nbaseamount']);
		}


		$chkSales = mysqli_query($con,"update salesreturn set ngross='$ngross', nbasegross='$nbasegross' where compcode='$company' and ctranno='$cSINo'");


	}
	


?>
