<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "Select * From customers_address Where compcode='$company' and ccode='".$_REQUEST["id"]."'"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['chouseno'] = $row['chouseno'];
			 $json['ccity'] = $row['ccity'];
			 $json['cstate'] = $row['cstate'];
			 $json['ccountry'] = $row['ccountry'];
			 $json['czip'] = $row['czip'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['chouseno'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
