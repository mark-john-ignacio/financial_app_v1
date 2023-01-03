<?php
//if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = '001';
	
	$result = mysqli_query ($con, "SELECT ccode, cgroupdesc FROM items_groups WHERE compcode='$company' and cgroupdesc like '%".$_REQUEST['query']."%' AND cstatus='ACTIVE' and cgroupno='".$_REQUEST['id']."'"); 
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['ccode'];
		 $json['name'] = $row['cgroupdesc'];
		 $json2[] = $json;

	}

	echo json_encode($json2);

?>
