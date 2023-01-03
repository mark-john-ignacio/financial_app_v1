<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select cname,cempid,nlimit from customers WHERE compcode='$company' and cname like '%".$_GET['query']."%' and cstatus='ACTIVE'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
		$c_id = $row['cempid'];
		if(!file_exists("../imgcust/".$c_id .".jpg")){
			$imgsrc = "../imgcust/emp.jpg";
		}
		else{
			$imgsrc = "../imgcust/".$c_id .".jpg";
		}

	    $json['cempid'] = $row['cempid'];
		$json['nlimit'] = $row['nlimit'];
     	$json['cname'] = utf8_encode($row['cname']);
		$json['imgsrc'] = $imgsrc;
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
