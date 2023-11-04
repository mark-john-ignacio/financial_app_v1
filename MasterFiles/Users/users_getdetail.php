<?php
include('../../Connection/connection_string.php');
	$result = mysqli_query ($con, "select * from users WHERE Userid = '".$_REQUEST['id']."'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	  $json['id'] = $row['Userid'];
    $json['fname'] = $row['Fname'];
		$json['mname'] = $row['Minit'];
		$json['lname'] = $row['Lname'];
		$json['emailadd'] = $row['cemailadd'];
		$json['cdepartment'] = $row['cdepartment'];
		$json['cdesignation'] = $row['cdesignation'];
		$json['imgsrc'] = $row['cuserpic'];
		$json['signsrc'] = $row['cusersign'];
		$json['usertype'] = $row['usertype'];
		$json2[] = $json;

	}


	echo json_encode($json2);
					
?>
