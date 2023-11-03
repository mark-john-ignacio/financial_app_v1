<?php
if(!isset($_SESSION)){
	session_start();
}

	require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$c_id = $_POST['c_id'];

	$result = mysqli_query($con,"SELECT * FROM accounts WHERE compcode='$company' and cacctid = '$c_id'"); 
	if (mysqli_num_rows($result)!=0) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$c_prodnme = $row['cacctdesc']; 	
		
	echo $c_prodnme;
	}
	else{
		echo "";
	}
	exit();  

?><!--<p><font color="#000000">recognize </font></p>-->
