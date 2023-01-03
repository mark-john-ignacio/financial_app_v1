<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	
		//$result = mysqli_query ($con, "Select A.typ, A.ccode, A.cname from (select 'CUSTOMER' as typ, cempid as ccode, cname from customers WHERE compcode='$company' and cname like '%".$_GET['query']."%' UNION ALL select 'SUPPLIER' as typ, ccode, cname from suppliers WHERE compcode='$company' and cname like '%".$_GET['query']."%') A Order By A.typ, A.cname");

		$result = mysqli_query ($con, "select 'SUPPLIER' as typ, ccode, cname from suppliers WHERE compcode='$company' and cname like '%".$_GET['query']."%'  Order By cname");



	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
 		 $json['typ'] = $row['typ'];
	     $json['id'] = $row['ccode'];
     	 $json['value'] = utf8_decode($row['cname']);
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
