<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select DISTINCT B.ctranno, B.ddate, B.ngross, B.ccurrencycode, B.nexchangerate, A.nqty, A.nfactor, C.nqty, C.nfactor from quote_t A left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno left join so_t C on A.compcode=C.compcode and A.ctranno=C.creference and A.citemno=C.citemno where A.compcode='".$company."' and B.lapproved=1 and B.ccode='".$_REQUEST['x']."' and B.csalestype='".$_REQUEST['selsi']."' and B.quotetype='quote' HAVING ((A.nqty*A.nfactor) - ifnull((C.nqty*C.nfactor),0)) > 0 order by B.ddate desc, A.ctranno desc"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['cpono'] = $row['ctranno'];
			 $json['dcutdate'] = $row['ddate'];
			 $json['ngross'] = $row['ngross'];
			 $json['ccurrencycode'] = $row['ccurrencycode'];
			 $json['nexchangerate'] = $row['nexchangerate'];
			 $json2[] = $json;
	
		}
	}
	else{
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
