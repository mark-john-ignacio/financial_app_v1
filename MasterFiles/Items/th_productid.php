<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$sql = "select * from items where compcode='$company' and cpartno = '".$_REQUEST['query']."' and cstatus='ACTIVE'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$json['id'] = $rs['cpartno'];
		$json['value'] = $rs['citemdesc'];
		$json['cunit'] = $rs['cunit'];
		$json2[] = $json;
	
	}


echo json_encode($json2);
//echo $sql;

?>
