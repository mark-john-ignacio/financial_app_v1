<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select B.ctranno, B.ddate, B.ngross, sum(A.nqty*A.nfactor), ifnull(sum(C.nqty*C.nfactor),0) from so_t A left join so B on A.compcode=B.compcode and A.ctranno=B.ctranno left join dr_t C on A.compcode=C.compcode and A.ctranno=C.creference and A.citemno=C.citemno and A.nident = C.crefident where A.compcode='".$company."' and B.lapproved=1 and B.ccode='".$_REQUEST['x']."' and B.csalestype='Goods' Group by B.ctranno, B.ddate, B.ngross HAVING (sum(A.nqty*A.nfactor) - ifnull(sum(C.nqty*C.nfactor),0)) > 0 order by B.ddate desc, A.ctranno desc"); 

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
