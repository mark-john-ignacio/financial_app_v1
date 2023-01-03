<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$sql = "select a.*,b.cname from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode where a.compcode='$company' and a.cpono LIKE '%".$_GET["query"]."%' and a.lcancelled=0 and a.lapproved=1";
	
	$result = mysqli_query ($con, $sql); 

	//$json2 = array();
	//$json = [];
	$f1 = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$f1 = $f1 + 1;

	     $json['cpono'] = $row['cpono'];
		 $json['dcutdate'] = $row['dcutdate'];
		 $json['ngross'] = $row['ngross'];
		 $json['ccode'] = $row['ccode'];
		 $json['cname'] = $row['cname'];
		 $json2[] = $json;

	}

	if ($f1==0){
		$json['cpono'] = "NONE";
		$json2[] = $json;
		
	}
	
	echo json_encode($json2);


?>
