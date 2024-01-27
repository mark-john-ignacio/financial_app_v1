<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "Select * From (SELECT 'trade' as typx, A.ctranno, A.dcutdate, A.ccode, B.cname, A.cremarks, A.csiprintno From sales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid Where A.compcode='$company' and A.ccode='".$_REQUEST['x']."' and A.lapproved=1 UNION ALL SELECT 'non_trade' as typx, A.ctranno, A.dcutdate, A.ccode, B.cname, A.cremarks, A.csiprintno From ntsales A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid Where A.compcode='$company' and A.ccode='".$_REQUEST['x']."' and A.lapproved=1) as a Order By a.typx, a.dcutdate desc"); 

		// "SELECT cacctno, cacctdesc, IFNULL(nbalance,0) as nbalance FROM accounts WHERE cacctdesc like '%".$_GET['query']."%' OR cacctno like '%".$_GET['query']."%'";
		
		//$json2 = array();
		//$json = [];
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

			$json['cpono'] = $row['ctranno'];
			$json['cutdate'] = $row['dcutdate'];
			$json['ccode'] = $row['ccode'];
			$json['cname'] = $row['cname'];
			$json['cremarks'] = $row['cremarks'];
			$json['csiprintno'] = $row['csiprintno'];
			$json['typx'] = $row['typx'];
			$json2[] = $json;

		}

		echo json_encode($json2);


?>
