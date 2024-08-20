<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$cid = $_REQUEST['id'];
	$ccode = $_REQUEST['code'];

	$path = "../../Components/assets/QO/".$company."_".$ccode."/".$cid;

	if (unlink($path)) {

			$json['message'] = 'Succesfully Delete!: ' . $cid;

	} else {
			$json['error'] = 'Error Deleteing File!: ' . $cid;
	}

	$json2[] = $json;
				
			echo json_encode($json2);


?>
