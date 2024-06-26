<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$id = $_REQUEST['id'];
	
	$result = mysqli_query ($con, "SELECT * FROM bank_check WHERE compcode='$company' and ccode = '$id'"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['chkbkno'] = $row['ccheckno'];
		 $json['chknofr'] = $row['ccheckfrom'];
		 $json['chknoto'] = $row['ccheckto'];
		 $json['chknocu'] = $row['ccurrentcheck'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
