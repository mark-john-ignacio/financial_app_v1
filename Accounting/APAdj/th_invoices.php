<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT A.ctranno, A.dreceived, A.ccode, B.cname, A.ngross From suppinv A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode Where A.compcode='$company' and A.ccode='".$_REQUEST['ccode']."' and ctranno like '%".$_REQUEST['query']."%' and A.lapproved=1 Order By A.dreceived DESC"); 

	// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	   $json['no'] = $row['ctranno'];
		 $json['cutdate'] = $row['dreceived'];
		 $json['ccode'] = $row['ccode'];
		 $json['cname'] = $row['cname'];
		 $json['ngross'] = number_format($row['ngross'],2);
		 $json2[] = $json;

	}



	echo json_encode($json2);


?>
