<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$arrrefsrs = array();
	$resreference = mysqli_query ($con, "Select crefsr from apadjustment where compcode='$company' and lcancelled=0");
	while($row = mysqli_fetch_array($resreference, MYSQLI_ASSOC)){
		$arrrefsrs[] = $row['crefsr'];
	}

	$arrsuppinvx = array();
	$resreference = mysqli_query ($con, "Select ctranno, crefrr from suppinv where compcode='$company' and lapproved=1");
	while($row = mysqli_fetch_array($resreference, MYSQLI_ASSOC)){
		$arrsuppinvx[] = $row;
	}

	$result = mysqli_query ($con, "Select A.ctranno, A.creference, B.dreturned from purchreturn_t A left join purchreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.ccode='".$_REQUEST['x']."' order by B.dreturned desc, A.ctranno desc"); 

	$f1 = 0;

	$json2 = array();
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$f1 = $f1 + 1;

			if(!in_array($row['ctranno'], $arrrefsrs)){
		
				$json['cpono'] = $row['ctranno'];
				$json['cref'] = $row['creference'];

				$refinvx = "";
				foreach($arrsuppinvx as $rs2){
					if($rs2['crefrr']==$row['creference']){
						$refinvx = $rs2['ctranno'];
					}
				}
				$json['crefinv'] = $refinvx;

				$json['dcutdate'] = $row['dreturned'];
				$json2[] = $json;

			}
	
		}
	}	
	
	echo json_encode($json2);


?>
