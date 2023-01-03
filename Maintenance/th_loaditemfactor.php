<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$id = $_REQUEST['id'];
	
	$result = mysqli_query ($con, "SELECT cunit, nfactor, cstatus FROM `items_factor` WHERE compcode='$company' AND cpartno='$id'"); 


	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['cunit'] = $row['cunit'];
		 $json['nfactor'] = $row['nfactor'];
		 $json['cstatus'] = $row['cstatus'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
