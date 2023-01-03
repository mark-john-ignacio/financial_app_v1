<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	
	$result = mysqli_query ($con, "Select * From items where compcode='$company' and cpartno='$y'"); 
	
	if(mysqli_num_rows($result)==0){
		
				echo "False";
	}
	else {
		
				
				echo "True";
		
		
	}

?>
