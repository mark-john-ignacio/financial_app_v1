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
	$crefnoident = chkgrp($_REQUEST['crefnoident']);
	$indexz = $_REQUEST['indx'];
	$cItemNo = $_REQUEST['citmno'];
	$nQty = $_REQUEST['nqty'];
	$nOrigQty = $_REQUEST['norigqty'];
	$cUnit = $_REQUEST['cuom'];
	$nPrice = $_REQUEST['nprice'];
	$nTransAmount = $_REQUEST['ntransamt'];
	$nAmount = $_REQUEST['namt'];

	$cSystemno = chkgrp($_REQUEST['nitemsysno']);
	$cSysPONo = chkgrp($_REQUEST['nitemposno']);
	
	$cMainUOM = $_REQUEST['mainunit'];
	$nFactor = $_REQUEST['nfactor'];
	$cacctcode = "NULL";
	$cacctcost = "NULL";
		
	$sqlhead = mysqli_query($con,"Select cacctcodedr, cacctcodecog from items where compcode='$company' and cpartno='$cItemNo'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cacctcode = "'".$row["cacctcodedr"]."'";
		$cacctcost = "'".$row["cacctcodecog"]."'";
	}

	$refcidenttran = $cSINo."P".$indexz;
	
	if (!mysqli_query($con,"INSERT INTO dr_t(`compcode`, `cidentity`, `ctranno`, `creference`, `crefident`, `nident`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `nbaseamount`, `namount`, `cmainunit`,`nfactor`,`cacctcode`,`cacctcost`, `citemsysno`, `citempono`) values('$company', '$refcidenttran', '$cSINo', $crefno, $crefnoident, '$indexz', '$cItemNo', '$nQty', '$nOrigQty', '$cUnit', '$nPrice', '$nTransAmount', '$nAmount', '$cMainUOM', $nFactor,$cacctcode,$cacctcost,$cSystemno,$cSysPONo)")){
		echo "False";
		
		//echo mysqli_error($con);
		
	}
	else{
		echo "True";
	}
	


?>
