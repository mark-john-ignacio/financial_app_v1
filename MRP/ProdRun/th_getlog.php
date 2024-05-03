<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	
	$company = $_SESSION['companyid'];
	$processid = $_REQUEST['processid'];

	$code = "";
	$nact = 0;
	$rxnum = 0;
	$sql = mysqli_query($con,"select ctranno from mrp_jo_process_t where compcode = '$company' and nid = '$processid'");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			$code = $row['ctranno'];
			if(floatval($row['nactualoutput']) > 0){
				$rxnum++;
			}
		}
	}

	
	$sql = mysqli_query($con,"select ctranno from mrp_jo_process_logs_actual where compcode = '$company' and ctranno='$code' and nrefid = '$processid'");

	if(mysqli_num_rows($sql) > 0){
		$rxnum = mysqli_num_rows($sql);
	}

	echo $rxnum;
		
?>
