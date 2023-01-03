<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	
	$result = mysqli_query ($con, "Select * From suppliers_groups where compcode='$company' and cgroupno='$y'"); 
	
	if(mysqli_num_rows($result)==0){
		
				echo "False";
	}
	else {
		
				
				echo "True";
		
		
	}




?>
