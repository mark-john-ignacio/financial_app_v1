<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if ($_REQUEST['y'] <> "") {
		$salesno = str_replace(",","','",$_REQUEST['y']);
		
		$qry = " and ctranno not in ('".$salesno."') ";
	}
	else {
		$qry = " ";
	}

	$acctsreciept = array();
	$result = mysqli_query ($con, "select cacctno from accounts_default where compcode='$company' and ccode='PAYONHAND'"); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$acctsreciept[] = $row['cacctno'];
	}

	$acctsorlist = array();
	$result = mysqli_query ($con, "select B.corno from deposit_t A left join deposit B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lcancelled=0"); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$acctsorlist[] = $row['corno'];
	}
	
	$result = mysqli_query ($con, "select * from receipt where compcode='$company' and lapproved=1 and ldeposited=0 and cacctcode in ('".implode("','", $acctsreciept)."') ".$qry." and ctranno not in ('".implode("','", $acctsorlist)."') order by cornumber"); 

	//$json2 = array();
	//$json = [];
	$cntr = 0;
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$cntr = $cntr + 1;
		
	  $json['ctranno'] = $row['ctranno'];
		$json['corno'] = $row['cornumber'];
		$json['dcutdate'] = $row['dcutdate'];
		$json['cpaymethod'] = ucwords($row['cpaymethod']);
		$json['namount'] = number_format($row['namount'],2);
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
