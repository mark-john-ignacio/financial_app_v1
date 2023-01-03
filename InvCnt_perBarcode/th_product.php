<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
				
			$sqlitm = "select A.cpartno, A.citemdesc, A.cunit
			from items A where A.compcode='$company' and A.citemdesc LIKE '%".$_GET['query']."%'";
		
			$rsditm = mysqli_query($con,$sqlitm);
			if(mysqli_num_rows($rsditm)>=1){
				
					while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
	
						$json['cscan'] = $row['cpartno'];
						$json['id'] = $row['cpartno'];
						$json['desc'] = $row['citemdesc'];
						$json['cunit'] = strtoupper($row['cunit']);
						
						$json2[] = $json;
				
					}
	
			
			}
	

	echo json_encode($json2);


?>
