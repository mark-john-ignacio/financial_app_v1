<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "APAdj_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "APAdj_cancel";
}

include('../../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

$SRRef = "";
$SIRef = "";
$isReturn = 0;

$sql = "select * from apadjustment where compcode='$company' and ctranno='$tranno'";
$result=mysqli_query($con,$sql);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$SRRef = $row['crefsr'];
	$SIRef = $row['crefsi'];
	$isReturn = $row['isreturn'];
}

if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update apadjustment set lapproved=1 where compcode='$company' and ctranno='$tranno'")){
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";

		mysqli_query($con,"DELETE FROM `glactivity` Where compcode='$company' and ctranno='$tranno'");

		mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company', 'ARADJ', '$tranno', B.dcutdate, A.cacctno, A.ctitle, A.ndebit, A.ncredit, 0, NOW() From apadjustment_t A left join apadjustment B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tranno'");

		//update qtyreturned sa RR kung purchreturn
		if($isReturn==1){
			mysqli_query($con,"UPDATE suppinv_t a JOIN purchasereturn_t b ON a.compcode=b.compcode and a.citemno=b.citemno and a.nident=b.nrefident and b.ctranno='$SRRef' SET a.nqtyreturned = b.nqty + a.nqtyreturned WHERE a.compcode='$company' and a.ctranno='$SIRef'");
		}
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'POSTED','JOURNAL ENTRY','$compname','Post Record')");

	}
	
}

if($_REQUEST['typ']=="CANCEL"){
	
	//echo $_REQUEST['x'];
	
		if (!mysqli_query($con,"Update aradjustment set lcancelled=1 where compcode='$company' and ctranno='$tranno'")){
			$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
			$status = "False";
		} 
		else {
			$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
			$status = "Cancelled";
			
			mysqli_query($con,"DELETE FROM `glactivity` Where compcode='$company' and ctranno='$tranno'");
			
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','JOURNAL ENTRY','$compname','Cancel Record')");

		}

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>
