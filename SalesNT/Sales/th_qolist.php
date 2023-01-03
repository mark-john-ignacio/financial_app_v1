<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from ntdr where compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' order by ddate desc, ctranno desc"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['cpono'] = $row['ctranno'];
			 $json['dcutdate'] = $row['ddate'];
			 $json['ngross'] = $row['ngross'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
