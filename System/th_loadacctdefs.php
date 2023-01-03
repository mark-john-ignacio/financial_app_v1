<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];

	$result = mysqli_query ($con, "Select A.nidentity, A.ccode, A.cdescription,A.cacctno,B.cacctid,B.cacctdesc From accounts_default A left join accounts B on A.compcode=B.compcode and  A.cacctno=B.cacctid where A.compcode='$company' order by A.nidentity"); 
	
	if(mysqli_num_rows($result)==0){
		
					$json['nidentity'] = "";
					$json['ccode'] = "";
					$json['cdesc'] =  "";
					$json['cacctcode'] = "";
					$json['ctitle'] = "";
					$json2[] = $json;
	}
	else {
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					$json['nidentity'] = $rowgrp['nidentity'];
					$json['ccode'] = $rowgrp['ccode'];
					$json['cdesc'] = $rowgrp['cdescription'];
					$json['cacctcode'] = $rowgrp['cacctid'];
					$json['ctitle'] = $rowgrp['cacctdesc'];
					$json2[] = $json;

		
			}
		
	}
	
				echo json_encode($json2);




?>
