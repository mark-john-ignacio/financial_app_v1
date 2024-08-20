<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "POS_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "POS_cancel";
}

require_once "../../include/denied.php";
require_once "../../include/access.php";




//POST RECORD
$tranno = $_REQUEST['x'];

$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

	$sqlcp = "select * from company where compcode='$company'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$rowcpnum = $rowcp['cpnum'];
			
		}
	}



if($_REQUEST['typ']=="POST"){
			
	if (!mysqli_query($con,"Update sales set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";

		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
		values('$company','$tranno','$preparedby',NOW(),'POSTED','SALES INVOICE','$compname','Post Record')");
		
	}
				

}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update sales set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
	}

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`, `cancel_rem`) 
	values('$company', '$tranno', '$preparedby', NOW(),'CANCELLED','SALES INVOICE','$compname','Cancel Record','".$_REQUEST['canmsg']."')");

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>