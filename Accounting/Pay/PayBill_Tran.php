<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "PayBill_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "PayBill_cancel";
}

include('../../include/access2.php');


//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update paybill set lapproved=1, dappcanby = NOW(), cappcanby = '$preparedby' where compcode='$company' and ctranno='$tranno'")){
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";		
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','PAY BILLS','$compname','Post Record')");

		//update suppinv
		$rrlist = array();
		$sqlchk = mysqli_query($con,"Select crefrr, napplied from paybill_t A left join paybill B on A.compcode=B.compcode and A.ctranno=B.ctranno Where A.compcode='$company' and A.ctranno='$tranno' and crefrr <> ''");
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$rrlist[] = array('crefrr' => $row['crefrr'], 'napplied' => $row['napplied']);
		}

		foreach($rrlist as $rsx){
			$sqlchk = mysqli_query($con,"Select npaidamount from suppinv where ctranno='".$rsx['crefrr']."'");
			$rowdetloc = $sqlchk->fetch_all(MYSQLI_ASSOC);
			foreach($rowdetloc as $row0){
				$npdamt = $row0['npaidamount'];

				$ntotpaid = floatval($npdamt) + floatval($rsx['napplied']);
				$con->query("Update suppinv set npaidamount = " .$ntotpaid. " Where ctranno='".$rsx['crefrr']."'");

			}

		}

	}
}

if($_REQUEST['typ']=="CANCEL"){
	
	if (!mysqli_query($con,"Update paybill set lcancelled=1, dappcanby = NOW(), cappcanby = '$preparedby' where compcode='$company' and ctranno='$tranno'")){
			$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
			$status = "False";
	} 
	else {
			$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
			$status = "Cancelled";
			
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','PAY BILLS','$compname','Cancel Record')");

	}

}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>
