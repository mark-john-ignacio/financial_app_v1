<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];

	$cSINo = mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayType =  mysqli_real_escape_string($con, $_REQUEST['selpaytype']);
	$cPayMethod =  mysqli_real_escape_string($con, $_REQUEST['selpayment']);
	$cORNo =  mysqli_real_escape_string($con, $_REQUEST['txtORNo']); 
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	
	$nGross = str_replace(",","",$nGross);
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	if (!mysqli_query($con, "UPDATE `receipt` set `ccode` = '$cCustID', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cpaymethod` = '$cPayMethod', `cpaytype` = '$cPayType', `cremarks` = '$cRemarks', `namount` = $nGross, `cacctcode` = '$cAcctNo', `cornumber` = '$cORNo' where `compcode`='$company' and `ctranno`= '$cSINo'")) {
				
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	

//DELETE CASH AND CHEQUE TABLE...
if (!mysqli_query($con, "DELETE FROM `receipt_cash_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
					printf("Errormessage: %s\n", mysqli_error($con));
}

if (!mysqli_query($con, "DELETE FROM `receipt_check_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
					printf("Errormessage: %s\n", mysqli_error($con));
}


if ($cPayMethod=="Cash") { //INSERT CASH DETAILS
	$cvar1000 = mysqli_real_escape_string($con, $_REQUEST['txtDenom1000']);
	if(is_numeric($cvar1000)){
				$namt = 1000*$cvar1000;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '1000', $cvar1000, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar500 = mysqli_real_escape_string($con, $_REQUEST['txtDenom500']);
	if(is_numeric($cvar500)){
				$namt = 500*$cvar500;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '500', $cvar500, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar200 = mysqli_real_escape_string($con, $_REQUEST['txtDenom200']);
	if(is_numeric($cvar200)){
				$namt = 200*$cvar200;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '200', $cvar200, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar100 = mysqli_real_escape_string($con, $_REQUEST['txtDenom100']);
	if(is_numeric($cvar100)){
				$namt = 100*$cvar100;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '100', $cvar100, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar50 = mysqli_real_escape_string($con, $_REQUEST['txtDenom50']);
	if(is_numeric($cvar50)){
				$namt = 50*$cvar50;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '50', $cvar50, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar20 = mysqli_real_escape_string($con, $_REQUEST['txtDenom20']);
	if(is_numeric($cvar20)){
				$namt = 20*$cvar20;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '20', $cvar20, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar10 = mysqli_real_escape_string($con, $_REQUEST['txtDenom10']);
	if(is_numeric($cvar10)){
				$namt = 10*$cvar10;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '10', $cvar10, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar5 = mysqli_real_escape_string($con, $_REQUEST['txtDenom5']);
	if(is_numeric($cvar5)){
				$namt = 5*$cvar5;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '5', $cvar5, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar1 = mysqli_real_escape_string($con, $_REQUEST['txtDenom1']);
	if(is_numeric($cvar1)){
				$namt = 5*$cvar1;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '1', $cvar1, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar025 = mysqli_real_escape_string($con, $_REQUEST['txtDenom025']);
	if(is_numeric($cvar025)){
				$namt = 0.25*$cvar025;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '0.25', $cvar025, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar010 = mysqli_real_escape_string($con, $_REQUEST['txtDenom010']);
	if(is_numeric($cvar010)){
				$namt = 0.10*$cvar010;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '0.10', $cvar010, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar005 = mysqli_real_escape_string($con, $_REQUEST['txtDenom005']);
	if(is_numeric($cvar005)){
				$namt = 0.05*$cvar005;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '0.05', $cvar005, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
}
elseif ($cPayMethod=="Cheque"){ //INSERT CHEQUE DETAILS
	$CHKbank = mysqli_real_escape_string($con, $_REQUEST['txtBankName']);
	$CHKdate = mysqli_real_escape_string($con, $_REQUEST['txtChekDate']);
	$CHKchkno = mysqli_real_escape_string($con, $_REQUEST['txtCheckNo']); 
	$CHKchkamt = mysqli_real_escape_string($con, $_REQUEST['txtCheckAmt']);
	$CHKchkamt = str_replace(",","",$CHKchkamt);
	
				if (!mysqli_query($con, "INSERT INTO `receipt_check_t`(`compcode`, `ctranno`, `cbank`, `ccheckno`, `ddate`, nchkamt) values('$company', '$cSINo', '$CHKbank', '$CHKchkno', STR_TO_DATE('$CHKdate', '%m/%d/%Y'), $CHKchkamt)")) {
					
					printf("INSERT INTO `receipt_check_t`(`compcode`, `ctranno`, `cbank`, `ccheckno`, `ddate`, nchkamt) values('$company', '$cSINo', '$CHKbank', '$CHKchkno', STR_TO_DATE('$CHKdate', '%m/%d/%Y'), $CHKchkamt)\n", mysqli_error($con));
					
					printf("Errormessage: %s\n", mysqli_error($con));
				} 

}


if (!mysqli_query($con, "DELETE FROM `receipt_sales_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
	printf("Errormessage: %s\n", mysqli_error($con));
} 

//INSERT SALES DETAILS if Sales and Sales Type
if($cPayType=="Sales"){	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$csalesno = mysqli_real_escape_string($con, $_REQUEST['txtcSalesNo'.$z]);
		$ndiscount = mysqli_real_escape_string($con, $_REQUEST['txtDiscount'.$z]);
		$ndue = mysqli_real_escape_string($con, $_REQUEST['txtDue'.$z]);
		$namount = mysqli_real_escape_string($con, $_REQUEST['txtApplied'.$z]);
		$namount = str_replace(",","",$namount);
		
		if($ndiscount==""){
			$ndiscount = 0;
		}

			
		$cnt = $cnt + 1;

			if (!mysqli_query($con, "INSERT INTO `receipt_sales_t`(`compcode`, `ctranno`, `csalesno`, `namount`, `ndiscount`, `ndue`) values('$company', '$cSINo', '$csalesno', $namount, $ndiscount, $ndue)")) {
				printf("INSERT INTO `receipt_sales_t`(`compcode`, `ctranno`, `csalesno`, `namount`, `ndiscount`, `ndue`) values('$company', '$cSINo', '$csalesno', $namount, $ndiscount, $ndue)\n");
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	
}
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','RECEIVE PAYMENT','$compname','Updated Record')");

?>
<form action="OR_edit2.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>