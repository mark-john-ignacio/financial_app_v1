<?php

	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];

	$citemno = $_REQUEST['x'];
	$cdesc = $_REQUEST['ver'];


	mysqli_query($con, "update mrp_bom_label set ldefault = 0 where compcode = '".$_SESSION['companyid']."' and citemno = '$citemno'");

	if (!mysqli_query($con, "update mrp_bom_label set ldefault = 1 where compcode = '".$_SESSION['companyid']."' and citemno = '$citemno' and nversion = ".$cdesc)) {
		
		echo "False";

	}
	else{
		echo "True";
	}



?>	

