<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	
	$company = $_SESSION['companyid'];
	$processid = $_REQUEST['processid'];
	$colnme = $_REQUEST['colnme'];
	$colval = $_REQUEST['colval'];

	if($colval==""){
		$colval = "NULL";
	}else{
		$colval = "'".$colval."'";
	}

	$code = "";
	$sql = mysqli_query($con,"select ctranno from mrp_jo_process_t where compcode = '$company' and nid = '$processid'");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			$code = $row['ctranno'];
		}
	}

	if (!mysqli_query($con,"UPDATE mrp_jo_process_t set `$colnme` = $colval where `compcode` = '$company' and `nid` = '$processid'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{

		$compname = php_uname('n');
		$preparedby = $_SESSION['employeeid'];

		if($colval=="NULL"){
			$msg = "Reset ".$colnme;
			$xevent = "RESET";
		}else{
			$msg = "Updated Record";
			$xevent = "UPDATED";
		}

		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`, `cancel_rem`) 
		values('$company','$code','$preparedby',NOW(),'$xevent','PRODUCTION RUN','$compname','$msg','".$_REQUEST['resetmsg']."')");
			
		echo "True";

	}


?>
