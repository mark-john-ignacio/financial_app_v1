<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$json2 = array();

	//rritems
	$rrdetails = array();
	$ponos = array();
	$resrr = mysqli_query ($con, "select * from receive_t WHERE compcode='$company' and ctranno = '".$_REQUEST['id']."'"); 
	while($rowrr = mysqli_fetch_array($resrr, MYSQLI_ASSOC)){
		if(!in_array($rowrr['creference'],$apponos)){
			$ponos[] = $rowrr['creference'];
		}
	}


	//po details
	$respo = mysqli_query ($con, "select ccurrencycode, nexchangerate, ccurrencydesc, cewtcode from purchase WHERE compcode='$company' and cpono in ('".implode("','", $ponos)."') order by ddate DESC LIMIT 1"); 
	while($porow = mysqli_fetch_array($respo, MYSQLI_ASSOC)){
		$json['currcode'] = $porow['ccurrencycode'];
		$json['currate'] = $porow['nexchangerate'];
		$json['currdesc'] = $porow['ccurrencydesc']; 
		$json['ewtcode'] = $porow['cewtcode']; 
	}
	
	$result = mysqli_query ($con, "Select A.*, B.cname From receive A left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode where A.compcode='".$company."' and A.ctranno='".$_REQUEST['id']."' and A.lvoid=0"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$f1 = $f1 + 1;
	
			$json['csono'] = $row['ctranno'];
			$json['dcutdate'] = date_format(date_create($row['dreceived']),"m/d/Y");
			$json['ccode'] = $row['ccode'];
			$json['cname'] = $row['cname'];
			$json['lapproved'] = $row['lapproved'];
			$json['lcancelled'] = $row['lcancelled'];
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
