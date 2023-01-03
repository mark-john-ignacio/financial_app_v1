<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$typ = $_REQUEST['typ'];
	
	$result = mysqli_query ($con, "SELECT ccode, cdesc FROM groupings WHERE compcode='$company' and ctype = '$typ'"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['nidentity'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
