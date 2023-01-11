<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$result = mysqli_query ($con, "select a.*,b.cname,b.cpricever,b.nlimit from quote a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.compcode='$company' and a.lapproved=1 and a.ctranno='".$_REQUEST['id']."' and a.quotetype='billing'"); 

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			 $json['ccode'] = $row['ccode'];
			 $json['cname'] = $row['cname'];
			 $json['csalestype'] = $row['csalestype'];
			 $json['ccurrency'] = $row['ccurrencycode'];
			 $json['nrate'] = $row['nexchangerate'];
			 $json['cpricever'] = $row['cpricever'];
			 $json['nlimit'] = $row['nlimit'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['ccode'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
