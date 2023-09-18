<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Journal_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Journal_cancel";
}

include('../../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');



if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update journal set lapproved=1, ddateposted = NOW() where compcode='$company' and ctranno='$tranno'")){
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','JOURNAL ENTRY','$compname','Post Record')");

	}
	
}

if($_REQUEST['typ']=="CANCEL"){
	
	//echo $_REQUEST['x'];
	
		if (!mysqli_query($con,"Update journal set lcancelled=1 where compcode='$company' and ctranno='$tranno'")){
			$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
			$status = "False";
		} 
		else {
			$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
			$status = "Cancelled";
			
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','JOURNAL ENTRY','$compname','Cancel Record')");

		}

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>
