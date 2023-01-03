<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from sales where compcode='$company' and lapproved=1 and ctranno like '%".$_REQUEST['query']."%' order by ddate desc, ctranno desc"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['cinvno'] = $row['ctranno'];
			 $json['dcutdate'] = date_format(date_create($row['ddate']),'m/d/Y');
			 $json['ngross'] = $row['ngross'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['cinvno'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
