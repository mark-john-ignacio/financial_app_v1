<?php
session_start();
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");

//$dmonth = "06";
//$dyear = "16";
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from receipt where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "OR".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>"; 2016-01-0001;
	//echo substr($lastSI,5,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "OR".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "OR".$dmonth.$dyear.$baseno;
	}
}

	
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
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	
	if (!mysqli_query($con, "INSERT INTO `receipt`(`compcode`, `ctranno`, `ccode`, `ddate`, `dcutdate`, `cpaymethod`, `cpaytype`, `cremarks`, `namount`, `napplied`, `cacctcode`, `ccustacctcode`, `cornumber`, `cpreparedby`) values('$company', '$cSINo', '$cCustID', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cPayMethod', '$cPayType', '$cRemarks', $nGross, $nApplied, '$cAcctNo', NULL, '$cORNo', '$preparedby')")) {
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
					printf("Errormessage: %s\n", mysqli_error($con));
				} 

}

//INSERT SALES DETAILS if Sales and Sales Type
$rowcntS = $_REQUEST['hdnrowcnt'];
if($rowcntS!=0){	
	$cnt = 0;	 
	for($z=1; $z<=$rowcntS; $z++){
		
		$csalesno = $_REQUEST['txtcSalesNo'.$z];
				
		$namount = $_REQUEST['txtSIGross'.$z];
		$nnetamt = $_REQUEST['txtnetvat'.$z];
		$nvat = $_REQUEST['txtvatamt'.$z];
		$ewtcode = $_REQUEST['txtnEWT'.$z];
		$ewtrate = $_REQUEST['txtnEWTRate'.$z];
		$ewtamt = $_REQUEST['txtnEWTAmt'.$z];
		//$ndiscount = mysqli_real_escape_string($con, $_REQUEST['txtDiscount'.$z]);
		
		$ndm = $_REQUEST['txtndebit'.$z];
		$ncm = $_REQUEST['txtncredit'.$z];
		$npayments = $_REQUEST['txtnpayments'.$z];
		$ndue = $_REQUEST['txtDue'.$z];
		$napplied = $_REQUEST['txtApplied'.$z];
		
		$cacctno = $_REQUEST['txtcSalesAcctNo'.$z];
					
		$cnt = $cnt + 1;

		$refcidenttran = $cSINo."P".$cnt;
		
			if (!mysqli_query($con, "INSERT INTO `receipt_sales_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `csalesno`, `namount`, `nnet`, `nvat`, `cewtcode`, `newtrate`, `newtamt`, `ndiscount`, `ndue`, `ndm`, `ncm`, `npayment`, `napplied`, `cacctno`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno', $namount, $nnetamt, $nvat, '$ewtcode', $ewtrate, $ewtamt, 0, $ndue, $ndm, $ncm, $npayments, $napplied, '$cacctno')")) {
				//echo("INSERT INTO `receipt_sales_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `csalesno`, `namount`, `nnet`, `nvat`, `cewtcode`, `newtrate`, `newtamt`, `ndiscount`, `ndue`, `ndm`, `ncm`, `npayment`, `napplied`, `cacctno`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno', $namount, $nnetamt, $nvat, '$ewtcode', $ewtrate, $ewtamt, 0, $ndue, $ndm, $ncm, $npayments, $napplied, '$cacctno')<br>");
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	
}

//INSERT OTHERS DETAILS
$rowcnt = $_REQUEST['hdnOthcnt'];

	if (!mysqli_query($con, "DELETE FROM `receipt_others_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

if($rowcnt!=0){	

	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cacctno = mysqli_real_escape_string($con, $_REQUEST['txtacctno'.$z]);
		$ctitle = mysqli_real_escape_string($con, $_REQUEST['txtacctitle'.$z]);
		$nOthDR = mysqli_real_escape_string($con, $_REQUEST['txtnotDR'.$z]);
		$nOthCR = mysqli_real_escape_string($con, $_REQUEST['txtnotCR'.$z]);
		
		$cnt = $cnt + 1;
		
		$refcidenttran = $cSINo."P".$cnt;

			if (!mysqli_query($con, "INSERT INTO `receipt_others_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`,  `ncredit`, `ndebit`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$cacctno', '$ctitle', $nOthCR, $nOthDR)")) {
				
				printf("INSERT INTO `receipt_others_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `cacctno`, `ctitle`,  `ncredit`, `ndebit`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$cacctno', '$ctitle', $nOthDR, $nOthCR)\n");
				
				printf("Errormessage: %s\n", mysqli_error($con));
			} 


	}
}


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO receipt_others_t(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','RECEIVE PAYMENT','$compname','Inserted New Record')");

?>
<form action="OR_edit2.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
   // document.forms['frmpos'].submit();
</script>