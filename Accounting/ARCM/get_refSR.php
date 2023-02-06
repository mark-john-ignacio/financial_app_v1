<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if($_REQUEST['typ']=="sales" || $_REQUEST['typ']=="ntsales"){
		$resmain = mysqli_query ($con, "select ctranno,ngross,dcutdate from ".$_REQUEST['typ']." WHERE compcode='$company' and ccode='".$_REQUEST['id']."' and ctranno like '%".$_REQUEST['query']."%'"); 
	}elseif($_REQUEST['typ']=="salesreturn" || $_REQUEST['typ']=="ntsalesreturn"){
		$resmain = mysqli_query ($con, "select ctranno,ngross,dreceived as dcutdate from ".$_REQUEST['typ']." WHERE compcode='$company' and ccode='".$_REQUEST['id']."' and ctranno like '%".$_REQUEST['query']."%' and ctranno not in (select crefno from aradj where compcode='$company' and lcancelled=0)"); 
	}

	$json2 = array();
	while($row = mysqli_fetch_array($resmain, MYSQLI_ASSOC)){
		
	  $json['id'] = $row['ctranno'];
    $json['value'] = number_format($row['ngross'],2);
		$json['ddate'] = $row['dcutdate'];
		$json2[] = $json;

	}

	echo json_encode($json2);


?>
