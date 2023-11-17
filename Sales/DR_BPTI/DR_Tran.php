<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "DR_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "DR_cancel";
}

require_once "../../include/denied.php";
require_once "../../include/access.php";

//POST RECORD
$tranno = $_REQUEST['x'];
$chkwarn = $_REQUEST['warn'];
$chkbal = $_REQUEST['bal'];

$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');

if($_REQUEST['typ']=="POST"){

//check muna kung kumpleto na serials para sa mga items na required ang serial

	$sqlhead = mysqli_query($con,"SELECT A.citemno, A.nqty, A.nfactor, A.cunit, ifnull(Sum(B.nqty),0) as recqty, A.nqty as nqty FROM `dr_t` A left JOIN `dr_t_serials` B on A.compcode=B.compcode and A.ctranno=B.ctranno left JOIN `items` C on A.compcode=C.compcode and A.citemno=C.cpartno Where A.compcode='$company' and A.ctranno='$tranno' and C.lserial = 1 Group By A.citemno, A.nqty, A.nfactor, A.cunit HAVING A.nqty <> ifnull(Sum(B.nqty),0)");
	if (mysqli_num_rows($sqlhead)!=0) {
			$msgz = "";
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
				$msgz = $msgz.$row["citemno"]." DR Qty (".$row["nqty"].") No.of serials (".$row["recqty"].")<br>";
				$status = "False";
			}
				
				$msgz = "Please Check Item's serials:<br>".$msgz;
	}else{


		if($chkwarn==1){ //Check if blocking is enabled... pag enabled chk limit b4 posting
			$sqlhead = mysqli_query($con,"select a.ngross, B.nlimit from dr a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid where a.ctranno = '$tranno' and a.compcode='$company'");
			if (mysqli_num_rows($sqlhead)!=0) {
				$row = mysqli_fetch_assoc($sqlhead);
				$grossxz = $row["ngross"];
				$limitxz = $row["nlimit"];
				
				if((float)$chkbal >= (float)$grossxz){
					
						if (!mysqli_query($con,"Update dr set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
							$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
							$status = "False";
						} 
						else {
							$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
							$status = "Posted";
						}
						
						mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
			values('$tranno','$preparedby',NOW(),'POSTED','DELIVERY RECEIPT','$compname','Post Record')");


				}
				else{
					$msgz = "<b>Blocked Delivery: </b>Credit limit balance is not enough!";
					$status = "False";
				}
			}

		}else{

					 if (!mysqli_query($con,"Update dr set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
							$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
							$status = "False";
						} 
						else {
							$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
							$status = "Posted";
						}
		}
	}
}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update dr set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
		
		mysqli_query($con,"Delete From tblinventory where compcode='$company' and ctranno='$tranno'"); 
		mysqli_query($con,"Delete From glactivity where compcode='$company' and ctranno='$tranno'");
	}

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','DELIVERY RECEIPT','$compname','Cancel Record')");

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>