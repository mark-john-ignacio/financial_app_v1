<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT nidentity, ccode, cdesc FROM groupings WHERE compcode='$company' and ctype = 'ITMPMVER'"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['ccode'];
		 $json['name'] = $row['cdesc'];
		 $json['ident'] = $row['nidentity'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
