<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
		
		$sql = "select cvatcode, cvatdesc from vatcode WHERE compcode='$company' and cvatcode = '".$_POST['q']."'";

//	echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	if(mysqli_num_rows($result)==0){
		echo "True";

	}
	else {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			echo "Code already in use for " . $row['cvatdesc'];	
		
		}
	}

?>
