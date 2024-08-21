<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "SI_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "SI_cancel";
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

	//check if balanace debit credit
	$sqlhead = mysqli_query($con,"Select sum(ndebit) as ndebit, sum(ncredit) as ncredit from sales_glactivity where compcode='$company' and ctranno='$tranno'");

	$ccontinue = "";

	if (mysqli_num_rows($sqlhead)!=0) {
		$sum=0;
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

			if(floatval($row['ndebit'])>0 && floatval($row['ncredit'])>0){
				$sum = floatval($row['ndebit']) - floatval($row['ncredit']);
				$sum = abs($sum);
			}else{
				$ccontinue = "FALSE";
			}
		}

		if($sum > 1){
			$ccontinue = "FALSE";
		}else{
			$ccontinue = "TRUE";
		}

	}else{
		$ccontinue = "FALSE";
	}


	if($ccontinue == "TRUE"){
			
		if (!mysqli_query($con,"Update sales set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
			$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
			$status = "False";
		} 
		else {

			$qrySI = "INSERT INTO `glactivity`(`compcode`, `cmodule`, `ctranno`, `ddate`, `acctno`, `ctitle`, `ndebit`, `ncredit`, `lposted`, `dpostdate`, `ctaxcode`) Select compcode, cmodule, ctranno, ddate, acctno, ctitle, ndebit, ncredit, lposted, dpostdate, ctaxcode From sales_glactivity where compcode='$company' and ctranno='$tranno'";

			if (!mysqli_query($con,$qrySI)){
				$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted! But there is a problem in the accounting entry!";
				$status = "Posted";
			}else{
				$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
				$status = "Posted";
			}

			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
			values('$tranno','$preparedby',NOW(),'POSTED','SALES INVOICE','$compname','Post Record')");
			
		}

	}else{
		$msgz = "<b>Error: </b>Please check your Accounting Entry!";
		$status = "False";
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

	mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','SALES INVOICE','$compname','Cancel Record')");

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>