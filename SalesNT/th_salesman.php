<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select A.* from salesman A WHERE A.compcode='$company' and A.cname like '%".$_GET['query']."%' and A.cstatus='ACTIVE'"); 


	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	     $json['id'] = $row['ccode'];
     	 $json['value'] = utf8_encode($row['cname']);
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
