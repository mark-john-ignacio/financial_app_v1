<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from salesreturn where compcode='$company' and lapproved=1 and ccode='".$_REQUEST['x']."' and ctranno not in (select crefSR from aradj_t A left join aradj B on A.compcode=B.compcode  and A.ctranno=B.ctranno where A.compcode='$company' and B.lcancelled=0) order by dreceived desc, ctranno desc"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['cpono'] = $row['ctranno'];
			 $json['dcutdate'] = $row['dreceived'];
			 $json['ngross'] = $row['ngross'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
