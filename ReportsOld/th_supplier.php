<?php
require_once "../Connection/connection_string.php";


	$result = mysqli_query ($con, "select * from suppliers WHERE cname like '%".$_GET['query']."%'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	     $json['id'] = $row['ccode'];
     	 $json['value'] = $row['cname'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
