<?php
session_start();
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

	$cSINo = mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	//$cPayType =  mysqli_real_escape_string($con, $_REQUEST['selpaytype']);
	$cPayType = "";
	$cPayMethod =  mysqli_real_escape_string($con, $_REQUEST['selpayment']);
	$cORNo =  mysqli_real_escape_string($con, $_REQUEST['txtORNo']); 
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$nGross = str_replace(",","",$nGross);

	$nApplied =  mysqli_real_escape_string($con, $_REQUEST['txtnApplied']);
	$nApplied = str_replace(",","",$nApplied);

	$cOTDesc = "";
	$cOTRef = "";
	if ($cPayMethod!=="Cash" && $cPayMethod!=="Cheque"){
		$cOTDesc = mysqli_real_escape_string($con, $_REQUEST['txtOTBankName']);
		$cOTRef = mysqli_real_escape_string($con, $_REQUEST['txtOTRefNo']);	
	}
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	
	if (!mysqli_query($con, "UPDATE `receipt` set `ccode` = '$cCustID', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cpaymethod` = '$cPayMethod', `cpaytype` = '$cPayType', `cremarks` = '$cRemarks', `namount` = $nGross, `napplied` = $nApplied, `cacctcode` = '$cAcctNo', `cpaydesc` = '$cOTDesc', `cpayrefno` = '$cOTRef' where `compcode`='$company' and `ctranno`= '$cSINo'")) {
				
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


//INSERT SALES DETAILS if Sales and Sales Type
$rowcntS = $_REQUEST['hdnrowcnt'];
if($rowcntS!=0){	

	if (!mysqli_query($con, "DELETE FROM `receipt_sales_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$cnt = 0;	 
	for($z=1; $z<=$rowcntS; $z++){
		
		$csalesno = $_REQUEST['txtcSalesNo'.$z];
				
		$namount = str_replace(",","",$_REQUEST['txtSIGross'.$z]);
		$ndm = str_replace(",","",$_REQUEST['txtndebit'.$z]);
		$ncm = str_replace(",","",$_REQUEST['txtncredit'.$z]);
		$npayments = str_replace(",","",$_REQUEST['txtnpayments'.$z]);

		$cvatcode = str_replace(",","",$_REQUEST['txtnvatcode'.$z]);
		$nvat = str_replace(",","",$_REQUEST['txtvatamt'.$z]);
		$nnetamt = str_replace(",","",$_REQUEST['txtnetvat'.$z]);

		$ewtcode = $_REQUEST['txtnEWT'.$z];
		$ewtrate = str_replace(",","",$_REQUEST['txtnEWTRate'.$z]);
		$ewtamt = str_replace(",","", $_REQUEST['txtnEWTAmt'.$z]);
				
		$ndue = str_replace(",","",$_REQUEST['txtDue'.$z]);
		$napplied = str_replace(",","",$_REQUEST['txtApplied'.$z]);
		
		$cacctno = $_REQUEST['txtcSalesAcctNo'.$z];
					
		$cnt = $cnt + 1;

		$refcidenttran = $cSINo."P".$cnt;

			if (!mysqli_query($con, "INSERT INTO `receipt_sales_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `csalesno`, `namount`, `ctaxcode`, `nnet`, `nvat`, `cewtcode`, `newtrate`, `newtamt`, `ndue`, `ndm`, `ncm`, `npayment`, `napplied`, `cacctno`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno', $namount, '$cvatcode', $nnetamt, $nvat, '$ewtcode', $ewtrate, $ewtamt, $ndue, $ndm, $ncm, $npayments, $napplied, '$cacctno')")) {
				
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	
}

//INSERT CM/DM REFERENCES
$rowcntcmdm = $_REQUEST['hdnrowcntcmdm'];
if($rowcntcmdm!=0){

	if (!mysqli_query($con, "DELETE FROM `receipt_deds` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}

	$cnt = 0;	 
	for($z=1; $z<=$rowcntS; $z++){

		$cnt++;

		$adjtype = $_REQUEST['hdnctypeadj'.$z];
		$adjrefsi = $_REQUEST['hdndetsino'.$z];
		$adjtrano = $_REQUEST['txtapcmdm'.$z];
		$adjdte = $_REQUEST['txtapdte'.$z];
		$adjgrss = str_replace(",","",$_REQUEST['txtapamt'.$z]);
		$adjremz = $_REQUEST['txtremz'.$z]; 
		$adjisgiven = $_REQUEST['hdnisgiven'.$z];

		$refcidenttran = $cSINo."P".$cnt;

		if (!mysqli_query($con, "INSERT INTO `receipt_deds`(`compcode`,`cidentity`,`nidentity`,`ctranno`,`aradjustment_ctype`,`aradjustment_ctranno`,`aradjustment_crefsi`,`aradjustment_dcutdate`,`aradjustment_ngross`,`cremarks`,`isgiven`) values('$company', '$refcidenttran', '$cnt', '$cSINo','$adjtype','$adjtrano','$adjrefsi', '$adjdte', $adjgrss, '$adjremz', '$adjisgiven')")){
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