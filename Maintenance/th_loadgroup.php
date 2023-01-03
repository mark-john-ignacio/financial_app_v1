<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$y = $_REQUEST['id'];
	
	$result = mysqli_query ($con, "Select * From parameters where compcode='$company' and ccode='$y'"); 
	
	if(mysqli_num_rows($result)==0){
		
				echo "False";
	}
	else {
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					echo $rowgrp['cvalue'];
		
			}
		
	}




?>
