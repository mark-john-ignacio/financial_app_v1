<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	$code = $_REQUEST['id'];
	$val = $_REQUEST['q'];
		
	$sql = "select * from groupings WHERE compcode='$company' and ctype='$code' and ccode='$val'";

	$result = mysqli_query ($con, $sql); 

	if(mysqli_num_rows($result)==0){
		echo "True";

	}
	else {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			echo "Code already in use for " . $row['cdesc'];	
		
		}
	}

?>
