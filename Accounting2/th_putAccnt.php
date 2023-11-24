<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con,  "select * from glactivity where compcode='$company' and ctranno='".$_POST['x']."'"); 

	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
	
			 $json['acctid'] = $row['acctno'];
			 $json['accttitle'] = $row['ctitle'];
			 $json['ndebit'] = $row['ndebit'];
			 $json['ncredit'] = $row['ncredit'];
			 $json2[] = $json;
	
		}
	}
	else{
			 $json['acctid'] = "NONE";
			  $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
