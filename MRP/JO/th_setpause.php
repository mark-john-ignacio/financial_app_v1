<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";
	
	$company = $_SESSION['companyid'];
	$processid = $_REQUEST['processid'];

	$arrstay = array("stat" => "", "msg" => "");

	$code = "";
	$ddatestart = "";
	$ddateend = "";
	$sql = mysqli_query($con,"select ctranno, IFNULL(ddatestart,'') as ddatestart, IFNULL(ddateend,'') as ddateend from mrp_jo_process_t where compcode = '$company' and nid = '$processid'");
	if(mysqli_num_rows($sql) != 0){
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			$code = $row['ctranno'];
			$ddatestart = $row['ddatestart'];
			$ddateend = $row['ddateend'];
		}
	}

	if($ddatestart!=""){
		if($ddateend!=""){
			$arrstay = array("stat" => "False", "msg" => "Cannot pause process: Process already ended!");
		}else{

			
			if (!mysqli_query($con,"UPDATE mrp_jo_process_t set `lpause` = 1 where `compcode` = '$company' and `nid` = '$processid'")) {
				$arrstay = array("stat" => "False", "msg" => mysqli_error($con));
			} 
			else{

				$compname = php_uname('n');
				$preparedby = $_SESSION['employeeid'];

				mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`, `cancel_rem`) 
				values('$company','$code','$preparedby',NOW(),'PAUSE','PRODUCTION RUN','$compname','Paused Process','".$_REQUEST['pausemsg']."')");
					
				$arrstay = array("stat" => "True", "msg" => "Process Succefully Resumed");

			}

		}
	}else{
		$arrstay = array("stat" => "False", "msg" => "Cannot pause process: Process not starting yet!");
	}

	echo json_encode($arrstay);


?>
