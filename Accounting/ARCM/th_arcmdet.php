<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select A.*, B.cname from aradj A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid WHERE A.compcode='$company' and ctranno = '".$_POST['code']."'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	  $json['id'] = $row['ctranno'];
		$json['ccode'] = $row['ccode'];
		$json['cname'] = $row['cname'];
		$json['crem'] = $row['cremarks'];
    $json['ngross'] = $row['ngross'];
		$json['ddate'] = date_format(date_create($row['dcutdate']),"m/d/Y");
		$json['crefno'] = $row['crefno'];
		$json['cwithref'] = $row['cwithref'];
		$json['lapproved'] = $row['lapproved'];
		$json['lcancelled'] = $row['lcancelled'];
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
