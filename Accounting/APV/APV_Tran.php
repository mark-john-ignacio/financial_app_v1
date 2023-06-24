<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "APV_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "APV_cancel";
}

include('../../include/access2.php');


//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update apv set lapproved=1 where compcode='$company' and ctranno='$tranno'")){
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) values('$tranno','$preparedby',NOW(),'POSTED','APV','$compname','Post Record')");
		
		
		//update total due sa suppinv
		$sqlhead = mysqli_query($con,"Select * from apv_d where compcode='$company' and ctranno='$tranno'");
		if (mysqli_num_rows($sqlhead)!=0) {
			while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

				mysqli_query($con,"UPDATE suppinv set ndue='".$row['ndue']."' where compcode='".$row['compcode']."' and ctranno='".$row['crefno']."'");

			}
		}
		
		//insert to gl
		mysqli_query($con,"DELETE FROM `glactivity` where compcode='".$row['compcode']."' and `ctranno` = '$tranno'");
		
		mysqli_query($con,"INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`) Select '$company','APV','$tranno',A.dapvdate,B.cacctno,B.ctitle,B.ndebit,B.ncredit,0,NOW() From apv A left join apv_t B on  A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tranno'");
		
	}


}

if($_REQUEST['typ']=="CANCEL"){
	
		if (!mysqli_query($con,"Update apv set lcancelled=1 where compcode='$company' and ctranno='$tranno'")){
			$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
			$status = "False";
		} 
		else {
			$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
			$status = "Cancelled";
			
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','APV','$compname','Cancel Record')");

		}

}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>
