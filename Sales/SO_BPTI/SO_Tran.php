<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "SO_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "SO_cancel";
}

require_once "../../include/denied.php";
require_once "../../include/access.php";

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update so set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$company', '$tranno','$preparedby',NOW(),'POSTED','SALES ORDER','$compname','Post Record')");

	}

}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update so set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
		
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`, `cancel_rem`) 
	values('$company', '$tranno','$preparedby',NOW(),'CANCELLED','SALES ORDER','$compname','Cancel Record','".$_REQUEST['canmsg']."')");

	}


}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);


?>