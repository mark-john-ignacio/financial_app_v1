<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$q = $_REQUEST['code'];
	$grp = $_REQUEST['grp'];
	
	$result = mysqli_query ($con, "Select * From customers_groups where compcode='$company' and ccode = '$q'"); 
	
	if(mysqli_num_rows($result)==0){
		
				echo "False";
	}
	else {
		
			while($rowgrp = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
					echo "Code in use for ".$rowgrp['cgroupdesc'];
		
			}
		
	}


?>
