<?php
require_once "../Connection/connection_string.php";


	$result = mysqli_query ($con, "select * from customers WHERE cname like '%".$_REQUEST['query']."%'"); 

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
     	 $json['value'] = utf8_encode($row['cname']);
		 $json['nlimit'] = $row['nlimit'];
		 $json['imgsrc'] = $imgsrc;
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
