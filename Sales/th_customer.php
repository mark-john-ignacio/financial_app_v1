<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select A.*, B.cname as smaname from customers A left join salesman B on A.csman=B.ccode WHERE A.compcode='".$company."' and A.cname like '%".$_GET['query']."%' and A.cstatus='ACTIVE'"); 

	//echo "select A.*, B.cname as smaname from customers A left join salesman B on A.csman=B.ccode WHERE A.compcode='".$company."' and A.cname like '%".$_GET['query']."%' and A.cstatus='ACTIVE'";

	$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
		if(!file_exists("../imgcust/".$row['cempid'] .".jpg")){
			$imgsrc = "../../images/emp.jpg";
		}
		else{
			$imgsrc = "../../imgcust/".$row['cempid'] .".jpg";
		}

	  $json['id'] = $row['cempid'];
    $json['value'] = utf8_encode($row['cname']);
		$json['nlimit'] = $row['nlimit'];
		$json['ncrlimit'] = 0;
		$json['cver'] = $row['cpricever'];
		$json['imgsrc'] = $imgsrc;
		$json['chouseno'] = $row['chouseno'];
		$json['ccity'] = $row['ccity'];
		$json['cstate'] = $row['cstate'];
		$json['ccountry'] = $row['ccountry'];
		$json['czip'] = $row['czip'];
		$json['csman'] = $row['csman'];
		$json['smaname'] = $row['smaname'];
		$json['cterms'] = $row['cterms'];
		$json['cdefaultcurrency'] = $row['cdefaultcurrency'];
		$json2[] = $json;

	}


	echo json_encode($json2);


?>
