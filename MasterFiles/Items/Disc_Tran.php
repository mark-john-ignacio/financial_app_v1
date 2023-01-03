<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "DISC_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "DISC_cancel";
}

require_once "../../include/denied.php";
require_once "../../include/access.php";

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update discounts set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
		echo "<b>ERROR: </b>There's a problem posting your transaction!";
	} 
	else {
		echo "<b>SUCCESS: </b>Your transaction is successfully posted!";
	}

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','DISCOUNTS','$compname','Post Record')");

$status = "Posted";
}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update discounts set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		echo "<b>ERROR: </b>There's a problem cancelling your transaction!";
	} 
	else {
		echo "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
	}

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','DISCOUNTS','$compname','Cancel Record')");

}

?>