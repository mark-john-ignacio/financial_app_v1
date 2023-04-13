<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$qrytrd = "";
	if($_GET['lst']<>""){
		$qrytrd = $qrytrd . " and ctranno not in ('". str_replace(",","','",$_GET['lst'])."')";
	}
	
	$result = mysqli_query ($con, "SELECT * FROM aradjustment WHERE compcode='$company' and ccode='".$_GET['code']."' and ctranno like '%".$_GET['query']."%' and lapproved=1 and isreturn = 0 ".$qrytrd); 
	
	//echo "SELECT * FROM apcm WHERE compcode='$company' and ccode='".$_GET['code']."' and ctranno like '%".$_GET['query']."%' and lapproved=1 and cwithref = 0".$qrytrd;
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	  	$json['id'] = $row['ctranno'];
		 $json['crem'] = $row['cremarks'];
		 $json['ngross'] = $row['ngross'];
		 $json['ddate'] = $row['dcutdate'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
