<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "InvAdj_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "InvAdj_cancel";
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
			
	if (!mysqli_query($con,"Update adjustments set lapproved=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";
		
		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
		values('$tranno','$preparedby',NOW(),'POSTED','INVENTORY ADJUSTMENT','$compname','Post Record')");


		//Update stockledger table

		//Delete muna para malinis ang tblinventory
		mysqli_query($con, "Delete from tblinventory Where compcode='$company' and ctranno='$tranno'");


		$UpdateItem = mysqli_query($con,"select A.*,B.dadjdate, B.section_nid from adjustments_t A left join adjustments B on A.compcode=B.compcode and A.ctranno=B.ctranno Where A.ctranno='$tranno'");		
		while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
			$secnid = $itmupdate['section_nid'];
			$itmpartno = $itmupdate['citemno'];
			$cunit = $itmupdate['cunit'];
			$ncost = 0;
			$nprice = 0;
			$nqty = $itmupdate['nqty'];
			$nactual = $itmupdate['nqtyactual'];
			$nadj = $itmupdate['nadj'];
			$lastdate = date("Y-m-d", strtotime($itmupdate['dadjdate']));
			$lastdatetime = date("Y-m-d", strtotime($itmupdate['dadjdate']))." 23:59:59";
			
			if($nadj!==0){
				//pag mayadjustment insert sa stock ledger table
				if(!mysqli_query($con,"INSERT INTO `tblinventory` (`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `nsection_id`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) VALUES('001', '$tranno', '$lastdatetime', '$lastdate', 'INVADJ', '$secnid', '$itmpartno', '$cunit', '$nadj', '$cunit', 1, '$nadj', '$ncost', '$nprice', 0, 0, 0)"))
				{
					printf("Errormessage: %s\n", mysqli_error($con));
				}
				
			}
		}
		
	}
				

}

if($_REQUEST['typ']=="CANCEL"){

	if (!mysqli_query($con,"Update adjustments set lcancelled=1 where compcode='$company' and ctranno='$tranno'")) {
		$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
		$status = "False";
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
		$status = "Cancelled";
	}

	mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`,`module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','INVENTORY ADJUSTMENT','$compname','Cancel Record')");

}

	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>