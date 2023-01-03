<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "InvCnt_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "InvCnt_cancel";
}

require_once "../include/denied.php";
require_once "../include/access.php";




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
			
	if (!mysqli_query($con,"Update invcount set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";

		
		//Insert na sa receive_t_serials
		$sql = "SELECT * FROM invcount_t where compcode='$company' and ctranno='$tranno'";
		$result=mysqli_query($con,$sql);
				
			if (!mysqli_query($con, $sql)) {
				printf("Errormessage: %s\n", mysqli_error($con));
			} 
					
			$indexz = 0;
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$indexz = $row['nidentity'];
				$refcidenttran = $tranno."P".$indexz;
				mysqli_query($con,"INSERT INTO receive_t_serials(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `cunit`, `nfactor`, `cmainunit`, `cserial`, `cbarcode`, `nlocation`, `dexpired`) values('$company', '$refcidenttran', '$tranno', '$indexz', '', '$indexz', '".$row['citemno']."', '".$row['nqty']."', '".$row['cunit']."', 1, '".$row['cunit']."', '".$row['cserial']."', '".$row['cbarcode']."', '".$row['nlocation']."', '".$row['dexpdte']."')");
				
			}
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
		values('$tranno','$preparedby',NOW(),'POSTED','INVENTORY COUNT','$compname','Post Record')");
		
	}
				

}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update invcount set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
	}

mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','INVENTORY COUNT','$compname','Cancel Record')");

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>