<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT csalesno, dcutdate, ngross FROM sales WHERE compcode='$company' and csalesno = '".$_GET['id']."'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['csalesno'] = $row['csalesno'];
		 $json['dcutdate'] = $row['dcutdate'];
		 $json['ngross'] = $row['ngross'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
