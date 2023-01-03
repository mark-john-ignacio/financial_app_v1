<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "PurchRet_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "PurchRet_cancel";
}

require_once "../../include/denied.php";
require_once "../../include/access.php";


//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


if($_REQUEST['typ']=="POST"){
//check muna kung kumpleto na serials para sa mga items na required ang serial

	$sqlhead = mysqli_query($con,"SELECT A.citemno, A.nqty, A.nfactor, A.cunit, Sum(B.nqty) as recqty FROM `purchreturn_t` A left JOIN `purchreturn_t_serials` B on A.compcode=B.compcode and A.ctranno=B.ctranno left JOIN `items` C on A.compcode=C.compcode and A.citemno=C.cpartno Where A.compcode='$company' and B.ctranno='$tranno' and C.lserial = 1 Group By A.citemno, A.nqty, A.nfactor, A.cunit HAVING A.nqty <> Sum(B.nqty)");
	if (mysqli_num_rows($sqlhead)!=0) {
			$msgz = "";
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
				$msgz = $msgz.$row["citemno"]." Purch.Return Qty (".$row["nqty"].") No.of serials (".$row["recqty"].")<br>";
				$status = "False";
			}
				
				$msgz = "Please Check Item's serials:<br>".$msgz;
	}else{

				if (!mysqli_query($con,"Update purchreturn set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
					$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
					$status = "False";
				} 
				else {
					$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
					$status = "Posted";

					mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
						values('$tranno','$preparedby',NOW(),'POSTED','PURCH RETURN','$compname','Post Record')");

				}
	}



}

if($_REQUEST['typ']=="CANCEL"){
	
	if (!mysqli_query($con,"Update purchreturn set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";

	mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$tranno','$preparedby',NOW(),'CANCELLED','PURCH RETURN','$compname','Cancel Record')");

	}


}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>
