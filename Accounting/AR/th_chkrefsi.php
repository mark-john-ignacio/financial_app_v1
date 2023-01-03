<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "select * from sales where compcode='$company' and lapproved=1 and ctranno = '".$_REQUEST['id']."'"); 

	$f1 = 0;

	if (mysqli_num_rows($result)!=0){
		echo "True";
	}
	else{
		echo "Invalid Invoice Number...";	
	}

?>
