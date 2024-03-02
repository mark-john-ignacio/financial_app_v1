<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT A.ctranno, A.cornumber, A.dcutdate, A.cpaymethod, A.cremarks, A.namount, CASE WHEN A.cpaymethod='cheque' THEN B.ccheckno Else C.crefno END as cpayrefno 
	From receipt A 
	LEFT JOIN receipt_check_t B ON A.compcode = B.compcode AND A.ctranno = B.ctranno
    LEFT JOIN receipt_opay_t C ON A.compcode = C.compcode AND A.ctranno = C.ctranno  
	WHERE A.compcode='$company' and A.ctranno = '".$_GET['id']."'"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$xcref = "";
		if(isset($arrchkrem[$row['ctranno']])){
			$xcref = $arrchkrem[$row['ctranno']];
		}

	     $json['ctranno'] = $row['ctranno'];
		 $json['corno'] = $row['cornumber'];
		 $json['dcutdate'] = $row['dcutdate'];
	     $json['cpaymethod'] = ucwords($row['cpaymethod']);
		 $json['cremarks'] = $row['cremarks'];
		 $json['namount'] = number_format($row['namount'],2);
		 $json['namountorig'] = $row['namount'];
		 $json['creference'] = $row['cpayrefno'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
