<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$arrchkrem = array();
	$result = mysqli_query ($con, "SELECT * FROM receipt_check_t WHERE compcode='$company'");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arrchkrem[$row['ctranno']] = $row['ccheckno'];
	}
	
	$result = mysqli_query ($con, "SELECT ctranno, cornumber, dcutdate, cpaymethod, cremarks, namount, cpayrefno FROM receipt WHERE compcode='$company' and ctranno = '".$_GET['id']."'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$xcref = "";
		if(isset($arrchkrem[$row['ctranno']])){
			$xcref = $arrchkrem[$row['ctranno']];
		}

	     $json['ctranno'] = $row['ctranno'];
		 $json['corno'] = $row['cornumber'];
		 $json['dcutdate'] = $row['dcutdate'];
	     $json['cpaymethod'] = ucwords($row['cpaymethod']);
		 $json['cremarks'] = $row['cremarks'];
		 $json['namount'] = number_format($row['namount'],2);
		 $json['namountorig'] = $row['namount'];
		 $json['creference'] = ($row['cpaymethod']=="cheque") ? $xcref : $row['cpayrefno'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
