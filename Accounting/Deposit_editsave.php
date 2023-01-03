<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');


$company = $_SESSION['companyid'];


	$cSINo = mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayMethod =  mysqli_real_escape_string($con, $_REQUEST['selpayment']);
	
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$nGross = str_replace(",","",$nGross);
	
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	
	if (!mysqli_query($con, "UPDATE `deposit` set `cortype` = '$cPayMethod', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cremarks` = '$cRemarks', `cacctcode` = '$cAcctNo', `namount` = $nGross where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	
	if (!mysqli_query($con, "DELETE FROM `deposit_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
					printf("Errormessage: %s\n", mysqli_error($con));
	}


//INSERT SALES DETAILS if Sales and Sales Type
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$csalesno = mysqli_real_escape_string($con, $_REQUEST['txtcSalesNo'.$z]);
				
		$cnt = $cnt + 1;

		 $refcidenttran = $cSINo."P".$cnt;

			if (!mysqli_query($con, "INSERT INTO `deposit_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `corno`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno')")) {
				printf("INSERT INTO `deposit_t`(`compcode`, `ctranno`, `corno`) values('$company', '$cSINo', '$csalesno')\n");
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','BANK DEPOSIT','$compname','Updated Record')");

?>
<form action="Deposit_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>