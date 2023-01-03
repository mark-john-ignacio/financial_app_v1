<?php
if(!isset($_SESSION)){
	session_start();
}

require_once "../Connection/connection_string.php";


	$company = '001';
	
	$result = mysqli_query ($con, "SELECT ctaxcode, cdesc, nrate FROM wtaxcodes WHERE compcode='$company' and (cdesc like '%".$_GET['query']."%' OR ctaxcode like '%".$_GET['query']."%') and cstatus='ACTIVE'"); 

	// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['ctaxcode'];
		 $json['desc'] = $row['cdesc'];
		 $json['rate'] = $row['nrate'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
