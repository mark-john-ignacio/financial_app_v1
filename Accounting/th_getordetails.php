<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT ctranno, cornumber, dcutdate, cpaymethod, cremarks, namount FROM receipt WHERE compcode='$company' and ctranno = '".$_GET['id']."'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['ctranno'] = $row['ctranno'];
		 $json['corno'] = $row['cornumber'];
		 $json['dcutdate'] = $row['dcutdate'];
	     $json['cpaymethod'] = ucwords($row['cpaymethod']);
		 $json['cremarks'] = $row['cremarks'];
		 $json['namount'] = number_format($row['namount'],2);
		 $json['namountorig'] = $row['namount'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
