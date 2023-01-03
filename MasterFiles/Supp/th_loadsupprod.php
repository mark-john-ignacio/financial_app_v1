<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$resultmain = mysqli_query ($con, "SELECT A.cpartno, A.cremarks, B.citemdesc FROM `items_suppliers` A left join `items` B on A.compcode=B.compcode and A.cpartno=B.cpartno WHERE A.compcode='$company' and A.ccode = '".$_REQUEST['id']."'"); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

	     $json['id'] = $row2['cpartno'];
		 $json['name'] = $row2['citemdesc'];
		 $json['remarks'] = $row2['cremarks'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
