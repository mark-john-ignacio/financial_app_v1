<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

//trancode: trancode, discnme: discnme, seldisctyp: seldisctyp, cinfofld: discval, discamt: discamt, discacctno: discacctno

		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		
		$discnme = $_REQUEST['discnme'];
		$seldisctyp = $_REQUEST['seldisctyp'];
		$discval = $_REQUEST['discval'];
		$discamt = $_REQUEST['discamt'];
		$discacctno = $_REQUEST['discacctno']; 
		$discitmno = $_REQUEST['discitmno']; 
		$discitmnoident = $_REQUEST['discitmnoident'];
		//$indexz = $indexz + 1;
		
		$refcidenttran = $cSINo."P".$indexz;

		if (!mysqli_query($con,"INSERT INTO ntsales_t_disc(`compcode`, `cidentity`, `nident`, `ctranno`, `citemnoident`, `citemno`, `discounts_list_code`, `cdisctype`, `nvalue`, `namount`, `cacctno`) values('$company', '$refcidenttran', '$indexz', '$cSINo', '$discitmnoident', '$discitmno', '$discnme', '$seldisctyp', '$discval', '$discamt', '$discacctno')")){
			echo "False";
		}
		else{
			echo "True";
		}
	

?>
