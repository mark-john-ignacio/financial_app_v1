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
	
	if ($_REQUEST['x'] <> "All") {		
		$qry2 = " and cpaymethod='".$_REQUEST['x']."'";
	}
	else {
		$qry2 = " ";
	}
	
	if ($_REQUEST['z'] <> "") {		
		$qry3 = " and cacctno <> '".$_REQUEST['z']."'";
	}
	else {
		$qry3 = " ";
	}
		
	
	$result = mysqli_query ($con, "select * from receipt where compcode='$company' and lapproved=1 and ldeposited=0 ".$qry2.$qry.$qry3."order by cornumber"); 

	//$json2 = array();
	//$json = [];
	$cntr = 0;
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$cntr = $cntr + 1;
		
	     $json['ctranno'] = $row['ctranno'];
		 $json['corno'] = $row['cornumber'];
		 $json['dcutdate'] = $row['dcutdate'];
		 $json['namount'] = $row['namount'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
