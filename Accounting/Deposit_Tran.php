<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "Deposit_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "Deposit_cancel";
}

include('../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update deposit set lapproved=1 where compcode='$company' and ctranno='$tranno'")){
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";		
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','BANK DEPOSIT','$compname','Post Record')");

				$sqlbody = mysqli_query($con,"select * from deposit_t where compcode='$company' and  ctranno = '$tranno' order by nidentity");

				if (mysqli_num_rows($sqlbody)!=0) {
					while($rowbody = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
								
						$cornum = $rowbody['corno'];
						mysqli_query($con,"Update receipt set ldeposited=1 where compcode='$company' and ctranno='$cornum'");
								
					}
				}


	}

}

if($_REQUEST['typ']=="CANCEL"){
	
	if (!mysqli_query($con,"Update deposit set lcancelled=1 where compcode='$company' and ctranno='$tranno'")){
			$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
			$status = "False";
	} 
	else {
			$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
			$status = "Cancelled";
			
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','BANK DEPOSIT','$compname','Cancel Record')");

	}

}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>
