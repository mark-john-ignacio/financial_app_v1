<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");

//$dmonth = "06";
//$dyear = "16";
$company = $_SESSION['companyid'];
$cSINo = $_REQUEST['txtcsalesno'] ;

	
	$cCode =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$cMemType = mysqli_real_escape_string($con, $_REQUEST['txtmemberid']);
	$cDeptID =  mysqli_real_escape_string($con, $_REQUEST['txtdeptid']); 
	$ncapshare =  mysqli_real_escape_string($con, $_REQUEST['txtcap']);
	$nyrs =  mysqli_real_escape_string($con, $_REQUEST['txtyrs']);
	$cpurpose =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$dbegin =  $_REQUEST['date_start']; 
	$dend = $_REQUEST['date_end']; 
	
	$cLoanType =  mysqli_real_escape_string($con, $_REQUEST['selloantyp']);
	$cPayType = mysqli_real_escape_string($con, $_REQUEST['selpaymet']);
	$cTerms = mysqli_real_escape_string($con, $_REQUEST['selloantrm']);
	$nIntRate = mysqli_real_escape_string($con, $_REQUEST['selintrate']);
	$nAmount = mysqli_real_escape_string($con, $_REQUEST['txtnObtain']);
	$nLoanAmt = mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$nAddFee = mysqli_real_escape_string($con, $_REQUEST['txtnadd']);
	$nTotInt = mysqli_real_escape_string($con, $_REQUEST['txtnIntRate']);
	$nTotAmtLoan = mysqli_real_escape_string($con, $_REQUEST['txtnPayAmt']);
	$nDedAmt = mysqli_real_escape_string($con, $_REQUEST['txtnDeduct']);

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	if (isset($_REQUEST['chkautoded'])){
		$autoded = 1;
	}
	else{
		$autoded = 0;
	}

		$chkCustAcct = mysqli_query($con,"select * from parameters WHERE compcode='$company' and ccode='LOANDED'");

		if (!mysqli_query($con, "select * from parameters WHERE compcode='$company' and ccode='LOANDED'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
						
		while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
			
				$hdnDedType = $rowaccnt['cvalue'];
	
		}

	
	if (!mysqli_query($con, "Update `loans` set `ccode` = '$cCode', `cyrs` = '$nyrs', `cdeptid` = '$cDeptID', `nmembertype` = '$cMemType', `ncapshare` = '$ncapshare', `cpurpose` = '$cpurpose', `dbegin` = STR_TO_DATE('$dbegin', '%m/%d/%Y'), `dend` = STR_TO_DATE('$dend', '%m/%d/%Y'), `cloantype` = '$cLoanType', `cpaytype` = '$cPayType', `cterms` = '$cTerms', `nintrate` = '$nIntRate', `nloaned` = '$nLoanAmt' , `namount` = '$nAmount', `naddfee` = '$nAddFee', `ntotint` = '$nTotInt', `npayamt` = '$nTotAmtLoan', `ndedamt` = $nDedAmt,  `cdedtype` = '$hdnDedType', `lautoded` = $autoded where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
		//echo "<br>"."Update `loans` set , `ccode` = '$cCode', `cyrs` = '$nyrs', `cdeptid` = '$cDeptID', `nmembertype` = '$cMemType', `ncapshare` = '$ncapshare', `cpurpose` = '$cpurpose', `dbegin` = STR_TO_DATE('$dbegin', '%m/%d/%Y'), `dend` = STR_TO_DATE('$dend', '%m/%d/%Y'), `cloantype` = '$cLoanType', `cpaytype` = '$cPayType', `cterms` = '$cTerms', `nintrate` = '$nIntRate', `nloaned` = '$nLoanAmt' , `namount` = '$nAmount', `naddfee` = '$nAddFee', `ntotint` = '$nTotInt', `npayamt` = '$nTotAmtLoan', `ndedamt` = $nDedAmt,  `cdedtype` = '$hdnDedType', `lautoded` = $autoded where `compcode` = '$company' and `ctranno` = '$cSINo'";
	} 
	


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','LOAN APP','$compname','Updated Record')");

?>
<form action="App_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>