<?php
if(!isset($_SESSION)){
	session_start();
}

require_once "../Connection/connection_string.php";


	$company = '001';
	
	$result = mysqli_query ($con, "SELECT cacctid, cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE compcode='$company' and (cacctdesc like '%".$_GET['query']."%' OR cacctid like '%".$_GET['query']."%') and ctype='Details'"); 
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	   $json['id'] = $row['cacctid'];
		 $json['idcode'] = $row['cacctno'];
		 $json['name'] = $row['cacctdesc'];
		 $json['balance'] = $row['nbalance'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
