<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from paybill where compcode='$company' and YEAR(dtrandate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "PV".$dmonth.$dyear."00001";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "PV".$dmonth.$dyear."00001";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "PV".$dmonth.$dyear.$baseno;
	}
}

	
	
	$cCustID = mysqli_real_escape_string($con, $_POST['txtcustid']);
	$cPayee = mysqli_real_escape_string($con, $_POST['txtpayee']);
	$cAcctNo = mysqli_real_escape_string($con, $_POST['txtcacctid']);
	$dDate = mysqli_real_escape_string($con, $_POST['date_delivery']);
	$nGross = mysqli_real_escape_string($con, $_POST['txtnGross']);

	$nGross = str_replace( ',', '', $nGross );


	$npaid = mysqli_real_escape_string($con, $_POST['txttotpaid']);	

	$npaid = str_replace( ',', '', $npaid );


	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	$paymeth = mysqli_real_escape_string($con, $_POST['selpayment']); 
	//$paytype = mysqli_real_escape_string($con, $_POST['selpaytype']); 
	$paytype = "apv";
	$particulars = mysqli_real_escape_string($con, $_POST['txtparticulars']);


	if($paymeth=="cash"){
		$dTranDate = mysqli_real_escape_string($con, $dDate);
	}else{
		$dTranDate = mysqli_real_escape_string($con, $_POST['txtChekDate']); 
	}
	


	if($paymeth=="cheque"){
		$cBankCode = mysqli_real_escape_string($con, $_POST['txtBank']);
		$cCheckNo = mysqli_real_escape_string($con, $_POST['txtCheckNo']);			

		$cPayRefNo = "";
	}else{
		$cBankCode = mysqli_real_escape_string($con, $_POST['txtBank']);
		$cCheckNo = "";	

		$cPayRefNo = mysqli_real_escape_string($con, $_POST['txtPayRefrnce']);
	}
	

	if (!mysqli_query($con, "INSERT INTO `paybill`(`compcode`, `ctranno`, `ccode`, `cpayee`, `cpaymethod`, `cbankcode`, `ccheckno`, `cacctno`, `cpayrefno`, `ddate`, `dcheckdate`, `ngross`, `npaid`, `cpreparedby`, `cparticulars`, `cpaytype`) values('$company', '$cSINo', '$cCustID', '$cPayee', '$paymeth', '$cBankCode', '$cCheckNo', '$cAcctNo', '$cPayRefNo', STR_TO_DATE('$dDate', '%m/%d/%Y'), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), $nGross, $npaid, '$preparedby', '$particulars', '$paytype')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	
	//INSERT APV DETAILS
	
	$rowcnt = $_POST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){ 
		
		
		$capvno = mysqli_real_escape_string($con, $_POST['cTranNo'.$z]);
		$crefrr = mysqli_real_escape_string($con, $_POST['cRefRRNo'.$z]);
		$dapvdate = $_POST['dApvDate'.$z];
		$namnt = mysqli_real_escape_string($con, $_POST['nAmount'.$z]);
		$namnt = str_replace( ',', '', $namnt );

		//$ndiscount = mysqli_real_escape_string($con, $_POST['nDiscount'.$z]);
		$ndiscount = 0;
		$nowed = mysqli_real_escape_string($con, $_POST['cTotOwed'.$z]);
		$nowed = str_replace( ',', '', $nowed );

		$napplied = mysqli_real_escape_string($con, $_POST['nApplied'.$z]);
		$napplied = str_replace( ',', '', $napplied );

		$caccno = mysqli_real_escape_string($con, $_POST['cacctno'.$z]); 
		$hdnewt = mysqli_real_escape_string($con, $_POST['napvewt'.$z]); 

		if($napplied<>0){
			
			$cnt = $cnt + 1;
			
			$refcidenttran = $cSINo."P".$cnt;
		
			
			if (!mysqli_query($con, "INSERT INTO `paybill_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `crefrr`, `capvno`, `dapvdate`, `namount`, `ndiscount`, `nowed`, `napplied`, `cacctno`, `newtamt`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$crefrr', '$capvno', STR_TO_DATE('$dapvdate', '%m/%d/%Y'), $namnt, $ndiscount, $nowed, $napplied, '$caccno', $hdnewt)")) {
			printf("Errormessage: %s\n", mysqli_error($con));
			} 

		
		}

	}


	$newchk = floatval($cCheckNo) + 1;
	mysqli_query($con,"UPDATE bank_check set ccurrentcheck='$newchk' where compcode='$company' and ccode='$cBankCode' and ccurrentcheck='$cCheckNo'");
	
	
	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','CHECK ISSUANCE','$compname','Inserted New Record')");

?>
<form action="PayBill_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>