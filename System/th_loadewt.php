<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$sql = "Select * From wtaxcodes where compcode='$company' and cstatus='ACTIVE' order by nident";	
	$rsd = mysqli_query($con,$sql);
	
	if(mysqli_num_rows($rsd)==0){
				$json['nident'] = "";
				$json['ctaxcode'] = "";
				$json['nrate'] = "";
				$json['nratedivisor'] = "";
				$json['cbase'] = ""; 
				$json['cdesc'] = "";
				$json['cacctno'] = "";
				$json['cstat'] = "";
				$json2[] = $json;		
	}
	else{
		while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
			//if($rs['nqty']>=1){
				$json['nident'] = $rs['nident'];
				$json['ctaxcode'] = $rs['ctaxcode'];
				$json['nrate'] = $rs['nrate'];
				$json['nratedivisor'] = $rs['nratedivisor'];
				$json['cbase'] = $rs['cbase']; 
				$json['cdesc'] = $rs['cdesc'];
				$json['cacctno'] = is_null($rs['cacctno']) ? "" : $rs['cacctno'];
				$json['cstat'] = $rs['cstatus'];
				$json2[] = $json;
			
		//	}
		
		}
	}


echo json_encode($json2);
//echo $sql;

?>
