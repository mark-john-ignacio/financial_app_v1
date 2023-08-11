<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];

	$cSINo = mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	$cReqBy =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dDateNeed = $_REQUEST['date_needed'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cSection =  mysqli_real_escape_string($con, $_REQUEST['selwhfrom']);

	if (!mysqli_query($con, "INSERT INTO `purchrequest`(`compcode`, `ctranno`, `dneeded`, `cremarks`, `cpreparedby`, `locations_id`) values('$company', '$cSINo', STR_TO_DATE('$dDateNeed', '%m/%d/%Y'),'$cRemarks', '$cReqBy', '$cSection')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
				
		$itmcode = mysqli_real_escape_string($con, $_REQUEST['txtitemcode'.$z]);
		$seluom = mysqli_real_escape_string($con, $_REQUEST['seluom'.$z]);		
		$nqty = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnqty'.$z]));		
		$nmainuom = mysqli_real_escape_string($con, $_REQUEST['hdnmainuom'.$z]);
		$nfactor = mysqli_real_escape_string($con, $_REQUEST['hdnfactor'.$z]);
		$drmrks = mysqli_real_escape_string($con, $_REQUEST['dremarks'.$z]);

		$refcidenttran = $cSINo."P".$z;
	
		if(!mysqli_query($con,"INSERT INTO `purchrequest_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `citemno`, `cunit`, `cmainunit`, `nfactor`, `nqty`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$itmcode', '$seluom', '$nmainuom', '$nfactor', '$nqty', '$drmrks')")){ 
			
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}
		
	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE REQUEST','$compname','Inserted New Record')");

?>
<form action="PR_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>