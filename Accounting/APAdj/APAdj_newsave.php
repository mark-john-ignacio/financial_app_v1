<?php
if(!isset($_SESSION)){
	session_start();
}
include('../../Connection/connection_string.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];

$chkSales = mysqli_query($con,"select * from apadjustment where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "PJ".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "PJ".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "PJ".$dmonth.$dyear.$baseno;
	}
}
	
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
	

	if (!mysqli_query($con, "INSERT INTO `apadjustment`(`compcode`, `ctranno`, `ccode`, `ddate`, `dcutdate`, `ctype`, `cremarks`, `ngross`, `crefsr`, `crefsi`, `isreturn`,`cpreparedby`) values('$company', '$cSINo', '$cCustID', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cSelType', '$cRemarks', '$ngross', '$cSRRef', '$cSIRef', $dret, '$preparedby')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];

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
	
		mysqli_query($con,"INSERT INTO `apadjustment_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$cacctno', '$cacctdesc', $ndebit, $ncredit, $crem)");

	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','AP ADJUSTMENT','$compname','Inserted New Record')");

?>
<form action="APAdj_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>