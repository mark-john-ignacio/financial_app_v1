<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	
	$sql = "select A.* from items A where A.compcode='$company' and A.citemdesc LIKE '%".$_REQUEST['query']."%' and A.cstatus='ACTIVE'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$json['id'] = $rs['cpartno'];
		$json['cname'] = $rs['citemdesc'];
		$json['cunit'] = $rs['cunit'];
		$json['cskucode'] = $rs['cskucode'];

		$json2[] = $json;
	
	}


echo json_encode($json2);
//echo $sql;

?>
