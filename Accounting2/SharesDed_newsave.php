<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from salarydeduct where compcode='$company' Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SD".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SD".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SD".$dmonth.$dyear.$baseno;
	}
}

	
	$cCust = $_REQUEST['txtcustid']; 
	$cDept = $_REQUEST['txtcdept'];
	$nAmount = $_REQUEST['txtAmount'];
	$nMonths = $_REQUEST['txtmonths'];
	$nDeductAmt = $_REQUEST['txtDeduct'];
	$cType = $_REQUEST['seltyp'];
	$cCutCode = $_REQUEST['selcut'];

	$preparedby = $_SESSION['employeeid'];
	
	
	$nCutNum = $nMonths * 2;

	//INSERT HEADER
	//mysqli_query($con,"INSERT INTO savingshares(`compcode`, `ctrannno`, `ctype`, `cutcode`, `cremarks`, `cpreparedby`) values('$company', '$cSINo', '$cType', '$cCutCode', '$cRemarks', '$preparedby')");

	if (!mysqli_query($con, "INSERT INTO `salarydeduct`
(`compcode`,`ctranno`,`ccode`,`ddate`,`cdeptname`,`ctype`,`namount`,`dcutcode`,`nmonths`,`ncutoffnum`,`ndeductamt`)
	values('$company', '$cSINo', '$cCust', NOW(), '$cDept', '$cType', $nAmount, '$cCutCode', $nMonths, $nCutNum, $nDeductAmt)")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
		
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','SALARY DEDUCTION','$compname','Inserted New Record')");

?>
<form action="SharesDed_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>