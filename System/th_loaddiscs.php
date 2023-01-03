<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$sql = "Select A.*, B.cacctdesc From discounts_list A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and cstatus='ACTIVE' order by nident";	
	$rsd = mysqli_query($con,$sql);
	
	if(mysqli_num_rows($rsd)==0){
				$json['nident'] = "";
				$json['ccode'] = "";
				$json['cdesc'] = "";
				$json['cacctno'] = "";
				$json['cacctdesc'] = ""; 
				$json['cstat'] = "";
				$json2[] = $json;		
	}
	else{
		while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
			//if($rs['nqty']>=1){
				$json['nident'] = $rs['nident'];
				$json['ccode'] = $rs['ccode'];
				$json['cdesc'] = $rs['cdesc'];
				$json['cacctno'] = $rs['cacctno'];
				$json['cacctdesc'] = $rs['cacctdesc']; 
				$json['cstat'] = $rs['cstatus'];
				$json2[] = $json;
			
		//	}
		
		}
	}


echo json_encode($json2);
//echo $sql;

?>
