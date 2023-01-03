<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	$grpno = $_REQUEST['grpno'];
	$citmno = $_REQUEST['itm'];
	
	$result = mysqli_query ($con, "Select A.cGroup".$y." as grpno,B.cgroupdesc From customers A left join customers_groups B on A.compcode=B.compcode And A.cGroup".$y."=B.ccode and cgroupno='$grpno' where A.compcode='$company' and A.cempid='$citmno'"); 
	
	if(mysqli_num_rows($result)==0){
		
						 $json['id'] = "";
						 $json['name'] = "";
						 $json2[] = $json;
	}
	else {
		
				
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

						 $json['id'] = $row['grpno'];
						 $json['name'] = $row['cgroupdesc'];
						 $json2[] = $json;
				
					}

		
		
	}
	
	echo json_encode($json2);




?>
