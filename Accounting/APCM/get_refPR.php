<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from purchreturn WHERE compcode='$company' and ccode = '".$_GET['id']."' and ctranno like '%".$_GET['query']."%'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	     $json['id'] = $row['ctranno'];
     	 $json['value'] = $row['ngross'];
		 $json['ddate'] = $row['dreturned'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
