<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT ctaxcode, cdesc, nrate, cbase, nratedivisor FROM wtaxcodes WHERE compcode='$company' and cdesc like '%".$_GET['query']."%' OR ctaxcode like '%".$_GET['query']."%'"); 

	// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['ctaxcode'] = $row['ctaxcode'];
		 $json['cdesc'] = $row['cdesc'];
		 $json['nrate'] = $row['nrate'];
		 $json['cbase'] = $row['cbase'];
		 $json['nratedivisor'] = $row['nratedivisor'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
