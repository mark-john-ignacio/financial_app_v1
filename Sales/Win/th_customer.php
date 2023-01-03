<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from customers WHERE compcode='$company' and cstatus='ACTIVE' and cname like '%".$_GET['query']."%'"); 

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
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
