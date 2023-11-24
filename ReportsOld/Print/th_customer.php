<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	
	if($_GET['id']=="Print/Receiving.php"){
	$result = mysqli_query ($con, "select ccode, cname from suppliers WHERE compcode='$company' and cname like '%".$_GET['query']."%' and cstatus='ACTIVE'"); 
	}else{
		$result = mysqli_query ($con, "select cempid as ccode, cname from customers WHERE compcode='$company' and cname like '%".$_GET['query']."%' and cstatus='ACTIVE'"); 
	}

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
	     $json['id'] = $row['ccode'];
     	 $json['value'] = utf8_encode($row['cname']);
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
