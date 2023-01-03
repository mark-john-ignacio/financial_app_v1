<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
$cSINo =  mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cTotDeb =  mysqli_real_escape_string($con, $_REQUEST['txtnDebit']);
	$cTotCrd =  mysqli_real_escape_string($con, $_REQUEST['txtnCredit']);
	//$cTotTax =  mysqli_real_escape_string($con, $_REQUEST['txtnTax']);
	$cTotTax = 0;
	
	//if($_REQUEST['lTaxInc']=="YES"){ 
	//	$lTaxInc = 1;
	//}
	//else{
		$lTaxInc = 0;
	//}
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	

	if (!mysqli_query($con, "INSERT INTO `journal`(`compcode`, `ctranno`, `ddate`, `djdate`, `cmemo`, `ltaxinc`, `ntotdebit`, `ntotcredit`,  	ntottax,`cpreparedby`) values('$company', '$cSINo', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cRemarks', $lTaxInc, $cTotDeb, $cTotCrd, $cTotTax, '$preparedby')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	
	$rowcnt = $_REQUEST['hdnACCCnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cacctno = mysqli_real_escape_string($con, $_REQUEST['txtcAcctNo'.$z]);
		$cacctdesc = mysqli_real_escape_string($con, $_REQUEST['txtcAcctDesc'.$z]);
		$ndebit= mysqli_real_escape_string($con, $_REQUEST['txtnDebit'.$z]);
		$ncredit = mysqli_real_escape_string($con, $_REQUEST['txtnCredit'.$z]);
		$nsub = mysqli_real_escape_string($con, $_REQUEST['txtnSub'.$z]);
		$crem = mysqli_real_escape_string($con, $_REQUEST['txtcRem'.$z]);
		
		if($nsub==""){
			$nsub = "NULL";
		}else{
			$nsub = "'".$nsub."'";
		}
		
		if($crem==""){
			$crem = "NULL";
		}else{
			$crem = "'".$crem."'";
		}

		$refcidenttran = $cSINo."P".$z;
	
		mysqli_query($con,"INSERT INTO `journal_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`, `csub`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$cacctno', '$cacctdesc', $ndebit, $ncredit, $nsub, $crem)");

	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','JOURNAL','$compname','Inserted New Record')");

?>
<form action="Journal_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>