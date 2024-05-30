<?php

	if(!isset($_SESSION)){
		session_start();		
	}

	include('../Connection/connection_string.php');

	$employeeid = isset($_SESSION['employeeid']) ? $_SESSION['employeeid'] : '';

	$stmt = $con->prepare("SELECT * FROM users WHERE Userid = BINARY ?");
	$stmt->bind_param("s", $employeeid);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	if(is_null($row['modify']) || $row['modify']==""){
		
		header("Location: index.php");

	}else{

		$now = time();
		$your_date = strtotime($row['modify']);

		$datediff = $now - $your_date;

		$days = round($datediff / (60 * 60 * 24));

		if($days>=30){
			header("Location: index.php");
		}
	}

?>
