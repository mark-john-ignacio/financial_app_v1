<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from customers_secondary WHERE compcode='".$company."' and cmaincode='".$_GET['cmain']."' and cname like '%".$_GET['query']."%' and cstatus='ACTIVE'"); 

	$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
	  	$json['id'] = $row['ccode'];
    	$json['value'] = utf8_encode($row['cname']);
		$json['cadd'] = utf8_encode($row['caddress']);
		$json['ccity'] = utf8_encode($row['ccity']); 
		$json['cstat'] = utf8_encode($row['cstate']); 
		$json['ccntr'] = utf8_encode($row['ccountry']); 
		$json['czips'] = utf8_encode($row['czip']); 
		$json['ctin'] = utf8_encode($row['ctin']);
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
