<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

if($_REQUEST['typ']=="POST"){
	$_SESSION['pageid'] = "InvAdj_post";
}

if($_REQUEST['typ']=="CANCEL"){
	$_SESSION['pageid'] = "InvAdj_cancel";
}

include('../include/access.php');

//POST RECORD
$tranno = $_REQUEST['x'];
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];
$compname = php_uname('n');


if($_REQUEST['typ']=="POST"){
	
	if (!mysqli_query($con,"Update adjustments set lapproved=1, cappcanby='$preparedby', dappcandate=NOW() where compcode='$company' and ctrancode='$tranno'")){
		$msgz = "<b>ERROR: </b>There's a problem posting your transaction!";
		$status = "False";		
	} 
	else {
		$msgz = "<b>SUCCESS: </b>Your transaction is successfully posted!";
		$status = "Posted";

		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$tranno','$preparedby',NOW(),'POSTED','INV ADJUSTMENT','$compname','Post Record')");

//Update stockledger table

//Delete muna para malinis ang tblinventory
mysqli_query($con, "Delete from tblinventory Where compcode='$company' and ctranno='$tranno'");


		$UpdateItem = mysqli_query($con,"select A.*,B.dmonth,B.dyear from adjustments_t A left join adjustments B on A.compcode=B.compcode and A.ctrancode=B.ctrancode Where A.ctrancode='$tranno'");
		
		while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
			
			$itmpartno = $itmupdate['citemno'];
			$cunit = $itmupdate['cunit'];
			$ncost = 0;
			$nprice = 0;
			$nqty = $itmupdate['nqty'];
			$nactual = $itmupdate['nactual'];
			$nadj = $itmupdate['nadj'];
			$a_date = $itmupdate['dyear']."-".$itmupdate['dmonth']."-01";
			$lastdate = date("Y-m-t", strtotime($a_date));
			$lastdatetime = date("Y-m-t", strtotime($a_date))." 23:59:59";
			
			if($nadj!=0){
			//pag mayadjustment insert sa stock ledger table
				if(!mysqli_query($con,"INSERT INTO `tblinventory` (`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) VALUES('001', '$tranno', '$lastdatetime', '$lastdate', 'BEG', '$itmpartno', '$cunit', '$nadj', '$cunit', 1, '$nadj', '$ncost', '$nprice', 0, 0, 0)"))
				{
					printf("Errormessage: %s\n", mysqli_error($con));
				}
				
			}
					
		}
	}


}

if($_REQUEST['typ']=="CANCEL"){
	
	if (!mysqli_query($con,"Update adjustments set lcancelled=1, cappcanby='$preparedby', dappcandate=NOW() where compcode='$company' and ctrancode='$tranno'")){
			$msgz = "<b>ERROR: </b>There's a problem cancelling your transaction!";
			$status = "False";
	} 
	else {
			$msgz = "<b>SUCCESS: </b>Your transaction is successfully cancelled!";
			$status = "Cancelled";
			
			mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$tranno','$preparedby',NOW(),'CANCELLED','INV ADJUSTMENT','$compname','Cancel Record')");

	}

}


	$json['ms'] = $msgz;
	$json['stat'] = $status;

	$json2[] = $json;
		 
	echo json_encode($json2);

?>

