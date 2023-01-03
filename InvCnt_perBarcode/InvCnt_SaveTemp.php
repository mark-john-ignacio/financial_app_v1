<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');


	$company = $_SESSION['companyid'];

		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cunit'];
		$cScan = $_REQUEST['cscan'];
			
	if (!mysqli_query($con,"INSERT INTO invcount_temp(`compcode`, `citemno`, `cscancode`, `cunit`, `nqty`, `ddate`) values('$company', '$cItemNo', '$cScan', '$cUnit', '$nQty', NOW())")){
		//echo "False";
		
		echo "Errormessage: %s\n", mysqli_error($con);
	}
	else{
		echo "True";
		
	}
	


?>
