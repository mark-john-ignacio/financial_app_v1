<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from customers WHERE compcode='$company' and cstatus='ACTIVE' and cname like '%".$_REQUEST['query']."%'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
		if(!file_exists("../imgemps/".$row['cempid'] .".jpg")){
			$imgsrc = "../images/emp.jpg";
		}
		else{
			$imgsrc = "../imgemps/".$row['cempid'] .".jpg";
		}

	     $json['id'] = $row['cempid'];
     	 $json['value'] = $row['cname'];
		 $json['nlimit'] = $row['nlimit'];
		 $json['imgsrc'] = $imgsrc;
		 $json['matrix'] = $row['cpricever'];
		 $json2[] = $json;

	}


	if(mysqli_num_rows($result) != 0){
		echo json_encode([
			'valid' => true,
			'data' =>$json2
		]);
	} else {
		echo json_encode([
			'valid' => false,
			'data'=> ""
		]);
	}
	


?>
