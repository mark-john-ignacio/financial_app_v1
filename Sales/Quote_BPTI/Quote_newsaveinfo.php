<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

		$cSINo = $_REQUEST['trancode'];
		$indexz = $_REQUEST['indx'];
		
		$cinfocode = $_REQUEST['citmno']; 
		$nrefident = $_REQUEST['nrefidx'];
		$cinfofld = $_REQUEST['citmfld'];
		$cinfovlue = $_REQUEST['citmvlz'];
		
		//$indexz = $indexz + 1;
		
		$refcidenttran = $cSINo."P".$indexz;

		if (!mysqli_query($con,"INSERT INTO quote_t_info(`compcode`, `cidentity`, `ctranno`, `nident`, `nrefident`, `citemno`, `cfldnme`, `cvalue`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$nrefident', '$cinfocode', '$cinfofld', '$cinfovlue')")){
			echo "False";
		}
		else{
			echo "True";
		}
	

?>
