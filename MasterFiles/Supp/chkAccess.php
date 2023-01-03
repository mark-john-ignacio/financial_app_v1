<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$employeeid = $_SESSION['employeeid'];
	$pageid = $_REQUEST['id'];
	
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = '$pageid'");

	if(mysqli_num_rows($sql) == 0){
	
		echo "False";
	}
	else{
		echo "True";
	}

?>
