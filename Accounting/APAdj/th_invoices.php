<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$json2 = array();
	
	$result = mysqli_query ($con, "SELECT A.ctranno, A.dreceived, A.ccode, B.cname, A.ngross, A.crefsi, A.cremarks, A.ccurrencycode From suppinv A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode Where A.compcode='$company' and A.ccode='".$_REQUEST['ccode']."' and A.lapproved=1 and A.lvoid=0 Order By A.dreceived DESC"); 

	// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	   $json['no'] = $row['ctranno'];
		 $json['crefsi'] = $row['crefsi'];
		 $json['cutdate'] = $row['dreceived'];
		 $json['ccode'] = $row['ccode'];
		 $json['cname'] = $row['cname'];
		 $json['ngross'] = number_format($row['ngross'],2);
		 $json['cremarks'] = $row['cremarks'];
		 $json['ccurrencycode'] = $row['ccurrencycode'];
		 $json2[] = $json;

	}



	echo json_encode($json2);


?>
