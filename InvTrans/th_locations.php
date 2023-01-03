<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
				
			$sqlitm = "select A.* from receive_putaway_location A where A.compcode='$company'";
		
			$rsditm = mysqli_query($con,$sqlitm);
			if(mysqli_num_rows($rsditm)>=1){
				
					while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
	
						$json['nid'] = $row['nid'];
						$json['cdesc'] = $row['cdesc'];
						
						$json2[] = $json;
				
					}
	
			
			}
	

	echo json_encode($json2);


?>
