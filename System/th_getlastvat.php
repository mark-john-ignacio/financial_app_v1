<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];

	$result = mysqli_query ($con, "Select * From vatcode where compcode='$company' ORDER BY nidentity DESC LIMIT 1"); 
	
	if(mysqli_num_rows($result)==0){
		
				echo "False";
	}
	else {
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					echo $rowgrp['nidentity'];

		
			}
		
	}


?>
