<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];

	$result = mysqli_query ($con, "Select * From taxcode where compcode='$company' order by nidentity"); 
	
	if(mysqli_num_rows($result)==0){
		
					$json['ctaxcode'] = "";
					$json['ctaxdesc'] =  "";
					$json['nrate'] = "";
					$json['nident'] = "";
					$json['cstat'] = "";
					$json2[] = $json;
	}
	else {
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					$json['ctaxcode'] = $rowgrp['ctaxcode'];
					$json['ctaxdesc'] = $rowgrp['ctaxdesc'];
					$json['nrate'] = $rowgrp['nrate'];
					$json['nident'] = $rowgrp['nidentity'];
					$json['cstat'] = $rowgrp['cstatus'];
					$json2[] = $json;

		
			}
		
	}
	
				echo json_encode($json2);




?>
