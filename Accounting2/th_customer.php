<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "Select A.ctyp, A.cempid, A.cname
		From (
		select 'CUST ID:' as ctyp, cempid, cname from customers where compcode='$company' and cstatus='ACTIVE'
		UNION ALL
		select 'SUPP ID:' as ctyp, ccode as cempid, cname from suppliers where compcode='$company' and cstatus='ACTIVE'
		) A WHERE A.cname like '%".$_GET['query']."%'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		 $json['typ'] = $row['ctyp'];
	     $json['id'] = $row['cempid'];
		 $json['value'] = $row['cname'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
