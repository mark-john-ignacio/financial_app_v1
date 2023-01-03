<?php
if(!isset($_SESSION)){
session_start();
}

if ($_REQUEST['code'] == "system") {
	require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT cempid FROM customers WHERE ccustomertype='TYP001'"); 
	$cntr = 0;
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $cntr += 1;

	}

	echo $cntr;
}
else{

	require_once "../Connection/connection_HRIS.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT emp_id FROM employee_personal_info where emp_status = 'Active'"); 
	$cntr = 0;
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $cntr += 1;

	}

	echo $cntr;

}
?>
