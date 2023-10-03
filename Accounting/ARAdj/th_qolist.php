<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$arrrefsrs = array();
	$resreference = mysqli_query ($con, "Select crefsr from aradjustment where compcode='$company' and lcancelled=0;");
	while($row = mysqli_fetch_array($resreference, MYSQLI_ASSOC)){
		$arrrefsrs[] = $row['crefsr'];
	}


	$result = mysqli_query ($con, "Select A.typx, A.ctranno, A.creference, A.dreceived, A.ccurrencycode From (select DISTINCT 'trade' as typx, A.ctranno, A.creference, B.dreceived, C.ccurrencycode from salesreturn_t A left join salesreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno left join sales C on A.compcode=C.compcode and A.creference=C.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and B.ccode='".$_REQUEST['x']."' UNION ALL select DISTINCT 'non-trade' as typx, A.ctranno, A.creference, B.dreceived, C.ccurrencycode from ntsalesreturn_t A left join ntsalesreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno left join ntsales C on A.compcode=C.compcode and A.creference=C.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and B.ccode='".$_REQUEST['x']."') A order by A.dreceived desc, A.ctranno desc"); 

	$f1 = 0;

	$json2 = array();
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;

			if(!in_array($row['ctranno'], $arrrefsrs)){
		
				$json['cpono'] = $row['ctranno'];
				$json['cref'] = $row['creference'];
				$json['dcutdate'] = $row['dreceived'];
				$json['typx'] = $row['typx'];
				$json['ccurrencycode'] = $row['ccurrencycode'];
				$json2[] = $json;

			}
	
		}
	}	
	
	echo json_encode($json2);


?>
