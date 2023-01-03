<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT A.cvalue, B.cacctdesc , IFNULL(B.nbalance,0) as nbalance FROM parameters A left join accounts B on A.compcode=B.compcode and A.cvalue=B.cacctno WHERE A.compcode='$company' and A.ccode like '".$_REQUEST['id']."'"); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['cvalue'];
		 $json['name'] = $row['cacctdesc'];
		 $json['balance'] = $row['nbalance'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
