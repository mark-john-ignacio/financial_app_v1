<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	
	$company = $_SESSION['companyid'];
	$processid = $_REQUEST['processid'];
	$actualcnt = $_REQUEST['actualoutput'];
	$actualoperator = $_REQUEST['actualoperator'];
	$preparedby = $_SESSION['employeeid'];

	$code = "";
	$sql = mysqli_query($con,"select ctranno from mrp_jo_process_t where compcode = '$company' and nid = '$processid'");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			$code = $row['ctranno'];
		}
	}
		
	if (!mysqli_query($con,"INSERT INTO mrp_jo_process_logs_actual (`compcode`, `ctranno`, `nrefid`, `cuserid`, `ddateupdate`, `nactualoutput`, `operator_id`) VALUES ('$company', '$code', '$processid', '$preparedby', NOW(), '$actualcnt', '$actualoperator')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{

		$compname = php_uname('n');
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$code','$preparedby',NOW(),'UPDATED','PRODUCTION RUN','$compname','Updated Actual Output/Operator')");
		
		echo "True";

	}
		
?>
