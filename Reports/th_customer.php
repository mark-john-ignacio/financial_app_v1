<?php
if(!isset($_SESSION)){
	session_start();
}

require_once "../Connection/connection_string.php";

$company = $_SESSION['companyid']; 

	$result = mysqli_query ($con, "select * from customers WHERE compcode='$company' and (cname like '%".$_REQUEST['query']."%' or ctradename like '%".$_REQUEST['query']."%')"); 

	//echo "select * from customers WHERE cname like '%".$_REQUEST['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
		//echo $row['cname']."<br>";
		
		if(!file_exists("../imgemps/".$row['cempid'] .".jpg")){
			$imgsrc = "../images/emp.jpg";
		}
		else{
			$imgsrc = "../imgemps/".$row['cempid'] .".jpg";
		}

	  $json['id'] = $row['cempid'];
    $json['value'] = $row['ctradename'];
		$json['nlimit'] = $row['nlimit'];
		$json['imgsrc'] = $imgsrc;
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
