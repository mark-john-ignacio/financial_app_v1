<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT cempid, cname FROM customers WHERE compcode='$company' and (cname like '%".$_GET['query']."%' OR cempid like '%".$_GET['query']."%') and cstatus='ACTIVE'"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['cempid'];
		 $json['name'] = utf8_encode($row['cname']);
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
