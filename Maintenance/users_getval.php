<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

	$employeeid = $_REQUEST['id'];
	$pageid = $_REQUEST['val'];

	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = '$pageid'");

	if(mysqli_num_rows($sql) == 0){
		echo  "False";
	}
	
	else {
		echo "True";
	}
?>
