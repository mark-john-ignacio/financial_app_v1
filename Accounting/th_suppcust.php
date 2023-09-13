<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
			
	$result = mysqli_query ($con, "select A.*, B.nrate from suppliers A left join wtaxcodes B on A.compcode=B.compcode and A.newtcode=B.ctaxcode WHERE A.compcode='$company' and A.cname like '%".$_GET['query']."%'");  

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		

	  $json['id'] = $row['ccode'];
    $json['value'] = utf8_encode($row['cname']);
		$json['cewtcode'] = $row['newtcode'];
		$json['newtrate'] = $row['nrate'];
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
