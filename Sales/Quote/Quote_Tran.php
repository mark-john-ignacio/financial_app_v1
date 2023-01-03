<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Quote_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Quote_cancel";
}

require_once "../../include/denied.php";
require_once "../../include/access.php";

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update quote set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','QUOTATION','$compname','Post Record')");

	}



}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update quote set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','QUOTATION','$compname','Cancel Record')");

	}


}

if($_REQUEST['typ']=="OPEN"){

	if (!mysqli_query($con,"Update quote set lcancelled=0,lapproved=0 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem opening your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully opened!";
		$status = "Opened";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'OPEN','QUOTATION','$compname','Open Record')");

	}

}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);


?>