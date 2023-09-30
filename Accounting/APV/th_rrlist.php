<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from suppinv where compcode='$company' and ctranno like '%".$_GET['query']."%' and lapproved=1  and lvoid=0 and ctranno not in (Select crefno from apv_d) and ccode='".$_GET['code']."'"); 

	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
	
			 $json['id'] = $row['ctranno'];
			 $json['value'] = $row['ngross'];
			 $json['label'] = $row['dreceived'];
			 $json2[] = $json;
	
		}
	}
	else{
			 $json['id'] = "NO AVAILABLE WRR";
			 $json['value'] = "";
			 $json['label'] = "";
			  $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
