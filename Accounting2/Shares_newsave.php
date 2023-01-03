<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from savingshares where compcode='$company' Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SS".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SS".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SS".$dmonth.$dyear.$baseno;
	}
}

	
	$cRemarks = mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cType = $_REQUEST['seltype'];
	$cCutCode = $_REQUEST['selcut'];

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER
	//mysqli_query($con,"INSERT INTO savingshares(`compcode`, `ctrannno`, `ctype`, `cutcode`, `cremarks`, `cpreparedby`) values('$company', '$cSINo', '$cType', '$cCutCode', '$cRemarks', '$preparedby')");

	if (!mysqli_query($con, "INSERT INTO savingshares(`compcode`, `ctranno`, `ctype`, `cutcode`, `ddate`, `cremarks`, `cpreparedby`) 
	values('$company', '$cSINo', '$cType', '$cCutCode', NOW(), '$cRemarks', '$preparedby')")) {
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
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','$cType','$compname','Inserted New Record')");

?>
<form action="Shares_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>