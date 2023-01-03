<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
$cSINo =  mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	//$cChkNo =  mysqli_real_escape_string($con, $_REQUEST['txtchkNo']); 
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	//INSERT HEADER	
	//mysqli_query($con,"INSERT INTO `apv`(`compcode`, `ctranno`, `ddate`, `dapvdate`, `ccode`, `cchkno`, `cpaymentfor`, `ngross`, `cpreparedby`) values('$company', '$cSINo', '$cCustID', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cCustID', '$cChkNo','$cRemarks', 0, '$preparedby')");
	

	if (!mysqli_query($con, "INSERT INTO `apv`(`compcode`, `ctranno`, `ddate`, `dapvdate`, `ccode`, `cpayee`, `cpaymentfor`, `ngross`, `cpreparedby`) values('$company', '$cSINo', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cCustID', '$cPayee','$cRemarks', $nGross, '$preparedby')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	
	$rowcnt = $_REQUEST['hdnRRCnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
		
		$crrno = mysqli_real_escape_string($con, $_REQUEST['txtrefno'.$z]);
		$suppsi = mysqli_real_escape_string($con, $_REQUEST['txtsuppSI'.$z]);
		$desc= mysqli_real_escape_string($con, $_REQUEST['txtrrdesc'.$z]);
		$amnt = mysqli_real_escape_string($con, $_REQUEST['txtnamount'.$z]);
		$remarks = mysqli_real_escape_string($con, $_REQUEST['txtremarks'.$z]);

		mysqli_query($con,"INSERT INTO `apv_d`(`compcode`, `nidentity`, `ctranno`, `crefno`, `crefinv`, `cdescription`, `namount`, `cremarks`) values('$company', '$z', '$cSINo', '$crrno', '$suppsi', '$desc', $amnt, '$remarks')");

	}
	
	//INSERT ACCNTS DETAILS
	
	$rowcnt = $_REQUEST['hdnACCCnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcrefrr'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtacctno'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtacctitle'.$z]);
		$ndebit = mysqli_real_escape_string($con,$_REQUEST['txtdebit'.$z]);
		$ncredit = mysqli_real_escape_string($con,$_REQUEST['txtcredit'.$z]);
		$nsubid = mysqli_real_escape_string($con,$_REQUEST['txtsubsid'.$z]);
		$cacctrem= mysqli_real_escape_string($con,$_REQUEST['txtacctrem'.$z]);

		mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `csubsidiary`, `ndebit`, `ncredit`) values('$company', '$z', '$cSINo', '$crefrr', '$cacctno', '$ctitle', '$cacctrem', '$nsubid', $ndebit, $ncredit)");

	}

	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','APV','$compname','Inserted New Record')");

?>
<form action="APV_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>