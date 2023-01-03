<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "Select A.*, B.cpricever, B.cname, C.cname as cdelname, D.cname as csmaname From ntso A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid left join customers C on A.compcode=C.compcode and A.cdelcode=C.cempid left join salesman D on A.compcode=D.compcode and A.csalesman=D.ccode where A.compcode='".$company."' and A.ctranno='".$_REQUEST['id']."'"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['csono'] = $row['ctranno'];
			 $json['dcutdate'] = date_format(date_create($row['dcutdate']),"m/d/Y");
			 $json['ccode'] = $row['ccode'];
			 $json['cname'] = $row['cname'];
			 $json['cdelcode'] = $row['cdelcode'];
			 $json['cdelname'] = $row['cdelname'];
			 $json['csalesman'] = $row['csalesman'];
			 $json['csmaname'] = $row['csmaname'];
			 $json['chouseno'] = $row['cdeladdno'];
			 $json['ccity'] = $row['cdeladdcity'];
			 $json['cstate'] = $row['cdeladdstate'];
			 $json['ccountry'] = $row['cdeladdcountry'];
			 $json['czip'] = $row['cdeladdzip'];
			 $json['lapproved'] = $row['lapproved'];
			 $json['lcancelled'] = $row['lcancelled'];
			 $json['ngross'] = $row['ngross'];
			 $json['cver'] = $row['cpricever'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
