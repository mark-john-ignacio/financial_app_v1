<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	
	$company = $_SESSION['companyid'];
	$processid = $_REQUEST['processid'];
	$nreject = $_REQUEST['nreject'];
	$nscrap = $_REQUEST['nscrap'];
	$cqc = $_REQUEST['cqc'];
	$cremarks = $_REQUEST['cremarks'];
	$preparedby = $_SESSION['employeeid'];

	$code = "";
	$sql = mysqli_query($con,"select ctranno from mrp_jo_process_t where compcode = '$company' and nid = '$processid'");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			$code = $row['ctranno'];
		}
	}
		
	if (!mysqli_query($con,"INSERT INTO mrp_jo_process_logs_qc (`compcode`, `ctranno`, `nrefid`, `cuserid`, `ddateupdate`, `nrejectqty`, `nscrapqty`, `cqcpostedby`, `cremarks`) VALUES ('$company', '$code', '$processid', '$preparedby', NOW(), '$nreject', '$nscrap', '$cqc', '$cremarks')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	else{

		$compname = php_uname('n');
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$code','$preparedby',NOW(),'UPDATED','QC REJECTS','$compname','Updated QC Rejects Module')");
		
		echo "True";

	}
		
?>
