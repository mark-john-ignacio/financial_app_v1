<?php
require_once "../../Connection/connection_string.php";
//$q = strtolower($_GET["q"]);

 $c_id = $_REQUEST['c_id'];
 $result = mysqli_query($con,"SELECT * FROM suppliers_contacts WHERE ccode = '$c_id'"); 
 	if (mysqli_num_rows($result)!=0) {

 		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
 
			 $json['cname'] = $row['cname']; 
			 $json['cdesig'] = $row['cdesignation']; 
			 $json['cdept'] = $row['cdept'];
			 $json['cemail'] = $row['cemail'];
			 $json2[] = $json;
		}	 
	
 		echo json_encode($json2);
 	}	
	else{
	 echo "";
	}
	 exit();  
 
?>
