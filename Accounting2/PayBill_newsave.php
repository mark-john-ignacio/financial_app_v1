<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

//$dmonth = date("m");
$dmonth = "01";
$dyear = date("Y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from paybill where compcode='$company' Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "CV".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>"; 2016-01-0001;
	//echo substr($lastSI,5,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,5,2) <> $dmonth){
		$cSINo = "CV".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "CV".$dmonth.$dyear.$baseno;
	}
}

	
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	$cChkNo =  mysqli_real_escape_string($con, $_REQUEST['txtchkNo']); 
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	//INSERT HEADER	
	//mysqli_query($con,"INSERT INTO `apv`(`compcode`, `ctranno`, `ddate`, `dapvdate`, `ccode`, `cchkno`, `cpaymentfor`, `ngross`, `cpreparedby`) values('$company', '$cSINo', '$cCustID', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cCustID', '$cChkNo','$cRemarks', 0, '$preparedby')");
	

	if (!mysqli_query($con, "INSERT INTO `paybill`(`compcode`, `ctranno`, `cchkno`, `ddate`, `dcvdate`, `ccode`, `cpayee`, `cpaymentfor`, `ngross`, `cpreparedby`, `cacctno`) values('$company', '$cSINo', '$cChkNo', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cCustID', '$cPayee', '$cRemarks', $nGross, '$preparedby', '$cAcctNo')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	
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
			if (!mysqli_query($con, "INSERT INTO `paybill_t`(`compcode`, `nidentity`, `ctranno`, `capvno`, `dapvdate`, `namount`, `ndiscount`, `nowed`, `napplied`) values('$company', '$cnt', '$cSINo', '$capvno', STR_TO_DATE('$dapvdate', '%m/%d/%Y'), $namnt, $ndiscount, $nowed, $napplied)")) {
			printf("Errormessage: %s\n", mysqli_error($con));
			} 

		
		}

	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PAY BILLS','$compname','Inserted New Record')");

?>
<form action="PayBill_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>