<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from accounts where compcode='$company' and cacctno = '".$_REQUEST['id']."'"); 

	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			 $json['cstat'] = "True";
			 $json['id'] = $row['cacctno'];
			 $json['name'] = $row['cacctdesc'];
			 $json2[] = $json;
	
		}
	}
	else{
			 $json['cstat'] = "Invalid Account Code...";
			 $json['id'] = "";
			 $json['name'] = "";
			 $json2[] = $json;
	}

echo json_encode($json2);
?>
