<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT 'trade' as typx, A.ctranno, A.dcutdate, A.ccode, B.cname From sales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid Where A.compcode='$company' and A.ccode='".$_REQUEST['ccode']."' and ctranno like '%".$_REQUEST['query']."%' and A.lapproved=1 and A.lvoid=0 UNION ALL SELECT 'non_trade' as typx, A.ctranno, A.dcutdate, A.ccode, B.cname From ntsales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid Where A.compcode='$company' and A.ccode='".$_REQUEST['ccode']."' and ctranno like '%".$_REQUEST['query']."%' and A.lapproved=1 and A.lvoid=0"); 

	// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
	
	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	   $json['no'] = $row['ctranno'];
		 $json['cutdate'] = $row['dcutdate'];
		 $json['ccode'] = $row['ccode'];
		 $json['cname'] = $row['cname'];
		 $json['typx'] = $row['typx'];
		 $json2[] = $json;

	}



	echo json_encode($json2);


?>
