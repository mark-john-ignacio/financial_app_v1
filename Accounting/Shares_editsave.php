<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
$cSINo = $_REQUEST['txtctranno'];

	
	$cRemarks = mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cType = $_REQUEST['seltype'];
	$cCutCode = $_REQUEST['selcut'];

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER
	//mysqli_query($con,"INSERT INTO savingshares(`compcode`, `ctrannno`, `ctype`, `cutcode`, `cremarks`, `cpreparedby`) values('$company', '$cSINo', '$cType', '$cCutCode', '$cRemarks', '$preparedby')");

	if (!mysqli_query($con, "UPDATE savingshares set `ctype`= '$cType', `cutcode` = '$cCutCode', `cremarks` =  '$cRemarks'  where `compcode` = '$company'  and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	


	// Delete previous details
	if (!mysqli_query($con, "Delete from savingshares_t Where compcode='$company' and ctranno='$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	
	//echo $_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cCustID = $_REQUEST['txtcustid'.$z];
		$nAmount = $_REQUEST['txtnamount'.$z];

		mysqli_query($con,"INSERT INTO savingshares_t(`compcode`, `ctranno`, `nidentity`, `ccode`, `namount`) values('$company', '$cSINo', $z, '$cCustID', $nAmount)");

	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','$cType','$compname','Updated Record')");

?>
<form action="Shares_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>