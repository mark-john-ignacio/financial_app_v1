<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "Select A.*, B.cname From receive A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode where A.compcode='".$company."' and A.ctranno='".$_REQUEST['id']."'"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['csono'] = $row['ctranno'];
			 $json['dcutdate'] = date_format(date_create($row['dreceived']),"m/d/Y");
			 $json['ccode'] = $row['ccode'];
			 $json['cname'] = $row['cname'];
			 $json['lapproved'] = $row['lapproved'];
			 $json['lcancelled'] = $row['lcancelled'];
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
