<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST1" || $_REQUEST['typ']=="POST2"){
	$_SESSION['pageid'] = "InvTrans_post";
}

if($_REQUEST['typ']=="CANCEL1" || $_REQUEST['typ']=="CANCEL2"){
	$_SESSION['pageid'] = "InvTrans_cancel";
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

	$invtype = "";
	$sqlcp = "select * from invtransfer where compcode='$company' and ctranno='$tranno'";
	$resultcp = mysqli_query ($con, $sqlcp); 
	if(mysqli_num_rows($resultcp)!=0){
		while($rowcp = mysqli_fetch_array($resultcp, MYSQLI_ASSOC)){
			
			$invtype = $rowcp['ctrantype'];
			$csection1 = $rowcp['csection1'];
			$csection2 = $rowcp['csection2'];
			
		}
	}

	$forcvhecking = 0;


if($_REQUEST['typ']=="POST1"){
			
	if (!mysqli_query($con,"Update invtransfer set lapproved1=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
		values('$tranno','$preparedby',NOW(),'POSTED1','INVENTORY TRANSFER','$compname','Post Record')");

		/*
		if($invtype!=="request"){

			$UpdateItem = mysqli_query($con,"Select A.citemno, B.linventoriable From invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='$tranno'");
			while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
				foreach($arrinvlvs as $rschklvl){
					if($rschklvl['cpartno']==$itmupdate['citemno'] && $rschklvl['section_nid']==$csection1){
						$forcvhecking = 1;
					}
				}
			}

		}
		*/
					
	}
}

if($_REQUEST['typ']=="CANCEL1"){

	if (!mysqli_query($con,"Update invtransfer set lcancelled1=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
	}

	mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED1','INVENTORY COUNT','$compname','Cancel Record')");

}

if($_REQUEST['typ']=="POST2"){

	//check inventory first
	
			
	if (!mysqli_query($con,"Update invtransfer set lapproved2=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
		values('$tranno','$preparedby',NOW(),'POSTED2','INVENTORY TRANSFER','$compname','Post Record')");

		/*
		if($invtype=="request"){
			$UpdateItem = mysqli_query($con,"Select A.citemno, B.linventoriable From invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='$tranno'");
			while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
				foreach($arrinvlvs as $rschklvl){
					if($rschklvl['cpartno']==$itmupdate['citemno'] && $rschklvl['section_nid']==$csection2){
						$forcvhecking = 1;
					}
				}
			}
		}
		*/
		
	}
				

}

if($_REQUEST['typ']=="CANCEL2"){

	if (!mysqli_query($con,"Update invtransfer set lcancelled2=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
	}

	mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED2','INVENTORY COUNT','$compname','Cancel Record')");

}

	$json['ms'] = $msgz;
	$json['stat'] = $status; 
	$json['check'] = $forcvhecking;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>