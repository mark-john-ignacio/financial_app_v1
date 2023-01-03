<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];

$chkSales = mysqli_query($con,"select * from apv where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "AP".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "AP".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "AP".$dmonth.$dyear.$baseno;
	}
}

	
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
		

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
	
//	$rowcnt = $_REQUEST['hdnACCCnt'];
		 
//	for($z=1; $z<=$rowcnt; $z++){
		
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