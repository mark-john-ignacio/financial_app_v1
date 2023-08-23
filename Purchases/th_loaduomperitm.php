<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$resultmain = mysqli_query ($con, "SELECT A.cunit, B.cDesc FROM items A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$_REQUEST['id']."'"); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

	     $json['id'] = $row2['cunit'];
		 $json['name'] = $row2['cDesc'];
		 $json2[] = $json;

	}
	
	$result = mysqli_query ($con, "SELECT A.cunit, B.cDesc FROM items_factor A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$_REQUEST['id']."' AND A.cstatus='ACTIVE'"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	  $json['id'] = $row['cunit'];
		$json['name'] = $row['cDesc'];
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
