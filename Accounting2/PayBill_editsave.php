<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

//$dmonth = date("m");
$cCVNo = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];

	
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	$cChkNo =  mysqli_real_escape_string($con, $_REQUEST['txtchkNo']); 
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	

	if (!mysqli_query($con, "UPDATE `paybill`set `cchkno` = '$cChkNo', `dcvdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ccode` = '$cCustID', `cpayee` = '$cPayee', `cpaymentfor` = '$cRemarks', `ngross` = $nGross, `cacctno` = '$cAcctNo' where `compcode` = '$company' and `ctranno` = '$cCVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS

	if (!mysqli_query($con, "DELETE FROM `paybill_t` Where `ctranno` = '$cCVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$capvno = mysqli_real_escape_string($con, $_REQUEST['cTranNo'.$z]);
		$dapvdate = $_REQUEST['dApvDate'.$z];
		$namnt = mysqli_real_escape_string($con, $_REQUEST['nAmount'.$z]);
		$ndiscount = mysqli_real_escape_string($con, $_REQUEST['nDiscount'.$z]);
		$nowed = mysqli_real_escape_string($con, $_REQUEST['cTotOwed'.$z]);
		$napplied = mysqli_real_escape_string($con, $_REQUEST['nApplied'.$z]);


		if($napplied<>0){
			
		$cnt = $cnt + 1;
		//mysqli_query($con, "INSERT INTO `paybill_t`(`compcode`, `nidentity`, `ctranno`, `capvno`, `dapvdate`, `namount`, `ndiscount`, `nowed`, `napplied`) values('$company', '$cnt', '$cSINo', '$capvno', STR_TO_DATE('$dapvdate', '%m/%d/%Y'), $namnt, $ndiscount, $nowed, $napplied)");
		echo $capvno." - ".$dapvdate."<br>";
			if (!mysqli_query($con, "INSERT INTO `paybill_t`(`compcode`, `nidentity`, `ctranno`, `capvno`, `dapvdate`, `namount`, `ndiscount`, `nowed`, `napplied`) values('$company', '$cnt', '$cCVNo', '$capvno', STR_TO_DATE('$dapvdate', '%m/%d/%Y'), $namnt, $ndiscount, $nowed, $napplied)")) {
			printf("Errormessage: %s\n", mysqli_error($con));
			} 

		
		}

	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cCVNo','$preparedby',NOW(),'UPDATED','PAY BILLS','$compname','Updated Record')");

?>
<form action="PayBill_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cCVNo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>