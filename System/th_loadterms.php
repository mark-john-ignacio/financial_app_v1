<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$code = $_REQUEST['id'];
	
	$result = mysqli_query ($con, "Select * From groupings where compcode='$company' and ctype='$code'"); 
	
	if(mysqli_num_rows($result)==0){
		
					$json['ccode'] = "";
					$json['cdesc'] =  "";
					$json['nident'] = "";
					$json['cstat'] = "";
					$json2[] = $json;
	}
	else {
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					$json['ccode'] = $rowgrp['ccode'];
					$json['cdesc'] = $rowgrp['cdesc'];
					$json['nident'] = $rowgrp['nidentity'];
					$json['cstat'] = $rowgrp['cstatus'];
					$json2[] = $json;

		
			}
		
	}
	
				echo json_encode($json2);




?>
