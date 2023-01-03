<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT ccode, cgroupdesc FROM suppliers_groups WHERE compcode='$company' AND cstatus='ACTIVE' and cgroupno='".$_REQUEST['id']."'"); 


	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['ccode'];
		 $json['name'] = $row['cgroupdesc'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
