<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT cacctno, cacctid, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE compcode='$company' and cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'"); 

	// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	  $json['id'] = $row['cacctid'];
		$json['name'] = $row['cacctdesc'];
		$json['balance'] = $row['nbalance'];
		$json2[] = $json;

	}

	echo json_encode($json2);


?>
