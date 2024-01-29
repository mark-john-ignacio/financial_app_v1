<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

$company = $_SESSION['companyid'];

	$cSINo =  mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	
	$dTranDate = $_REQUEST['date_delivery']; 
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']);
	$cSelType =  mysqli_real_escape_string($con, $_REQUEST['seltype']);   
	$cSRRef =  mysqli_real_escape_string($con, $_REQUEST['txtSIRef']);
	$cSIRef =  mysqli_real_escape_string($con, $_REQUEST['txtInvoiceRef']);
	$ngross =  mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnGross']));
	
	$dret = 0;
	if(isset($_REQUEST['isReturn'])){
		$dret = 1;
	}

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	if (!mysqli_query($con, "UPDATE `aradjustment` set `ccode` = '$cCustID', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ctype` = '$cSelType', `cremarks` = '$cRemarks', `ngross` = '$ngross', `crefsr` = '$cSRRef', `crefsi` = '$cSIRef', `isreturn` = '$dret' where `compcode`='$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


		//DELETE Details
		if (!mysqli_query($con, "UPDATE `aradjustment_t` set ctranno='xxx', compcode='xxx', cidentity=CONCAT(cidentity,'xxx') where `compcode`='$company' and `ctranno`= '$cSINo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		}
	
	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$isok = "YES";

	$ntotalewt = 0;
	$ntotaltax = 0;

	for($z=1; $z<=$rowcnt; $z++){
		
		$cacctno = mysqli_real_escape_string($con, $_REQUEST['txtcAcctNo'.$z]);
		$cacctdesc = mysqli_real_escape_string($con, $_REQUEST['txtcAcctDesc'.$z]);
		$ndebit= mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnDebit'.$z]));
		$ncredit = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnCredit'.$z]));
		$crem = mysqli_real_escape_string($con, $_REQUEST['txtcRem'.$z]);

		if($crem==""){
			$crem = "NULL";
		}else{
			$crem = "'".$crem."'";
		}

		$refcidenttran = $cSINo."P".$z;
	
		if (!mysqli_query($con,"INSERT INTO `aradjustment_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$cacctno', '$cacctdesc', $ndebit, $ncredit, $crem)")){
			$isok = "No";
		}

		if($cacctno==@$ewtpaydef && floatval($ncredit) > 0){
			$ntotalewt = $ntotalewt + floatval($ncredit);
		}

		if($cacctno==@$OTpaydef && floatval($ndebit) > 0){
			$ntotaltax = $ntotaltax + floatval($ndebit);
		}

	}

	//update total vat and EWT
	mysqli_query($con, "UPDATE aradjustment set ntotvat=".$ntotaltax.", ntotewt=".$ntotalewt." where compcode = '$company' and ctranno='$cSINo'");
	

	if($isok=="YES"){
		mysqli_query($con, "DELETE FROM `aradjustment_t` where `compcode`='xxx' and `ctranno`= 'xxx'");
	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','AR ADJUSTMENT','$compname','Updated Record')");

?>
<form action="ARAdj_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
  document.forms['frmpos'].submit();
</script>