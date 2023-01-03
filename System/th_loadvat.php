<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

		$sql = "Select * From vatcode where compcode='$company' and cstatus='ACTIVE'";

	//echo $sql;
	
	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		
		//if($rs['nqty']>=1){
			$json['nident'] = $rs['nidentity'];
			$json['cvatcode'] = $rs['cvatcode'];
			$json['cvatdesc'] = $rs['cvatdesc'];
			$json['nrem'] = $rs['cremarks'];
			$json['lcomp'] = $rs['lcompute']; 
			$json['cstat'] = $rs['cstatus'];
			$json2[] = $json;
		
	//	}
	
	}


echo json_encode($json2);
//echo $sql;

?>
