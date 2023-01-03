<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$csalesno = $_REQUEST['id'];
		
		$sql = "select X.citemno as cpartno, A.citemdesc, X.cfldnme, X.cvalue
		from ntsales_t_info X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		where X.compcode='$company' and X.ctranno = '$csalesno'";

	//echo $sql;
	
	$resultmain = mysqli_query ($con, $sql); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

		$json['id'] = $row2['cpartno'];
		$json['desc'] = $row2['citemdesc'];
		$json['fldnme'] = $row2['cfldnme'];
		$json['cvalue'] = $row2['cvalue'];
		
		$json2[] = $json;

	}
	

	echo json_encode($json2);


?>
