<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$sql = "Select * From contacts_types where compcode='$company' order by cid";	
	$rsd = mysqli_query($con,$sql);
	
	if(mysqli_num_rows($rsd)==0){
				$json['cid'] = "";
				$json['cdesc'] = "";
				$json['cstat'] = "";
				$json2[] = $json;		
	}
	else{
		while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {

				$json['cid'] = $rs['cid'];
				$json['cdesc'] = $rs['cdesc'];
				$json['cstat'] = $rs['cstatus'];
				$json2[] = $json;
		
		}
	}

	echo json_encode($json2);

?>
