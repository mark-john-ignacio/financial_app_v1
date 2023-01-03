<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$qry = "select B.cpono as ctranno, B.ddate, B.ngross, sum(A.nqty*A.nfactor), ifnull(sum(C.nqty*C.nfactor),0) from purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono left join receive_t C on A.compcode=C.compcode and A.cpono=C.creference and A.citemno=C.citemno and A.nident = C.nrefidentity where A.compcode='$company' and B.lapproved=1 and B.ccode='".$_REQUEST['x']."' Group by B.cpono, B.ddate, B.ngross HAVING (sum(A.nqty*A.nfactor) - ifnull(sum(C.nqty*C.nfactor),0)) > 0 order by B.ddate desc, A.cpono desc ";
	
	$result = mysqli_query ($con, $qry); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;
	
			 $json['cpono'] = $row['ctranno'];
			 $json['dcutdate'] = $row['ddate'];
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
