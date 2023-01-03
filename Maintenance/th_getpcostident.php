<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "Select nidentity From items_purch_cost where compcode='$company' Order by nidentity desc LIMIT 1"); 
	
	if(mysqli_num_rows($result)==0){
		
				echo 1;
	}
	else {
		
				
				$rowqry = mysqli_fetch_assoc($result);
				
				echo $rowqry['nidentity'];
		
		
	}




?>
