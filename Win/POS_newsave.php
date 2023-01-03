<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from sales where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ddate desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SI".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['csalesno'];
	}
	
	echo $lastSI."<br>";
	echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SI".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SI".$dmonth.$dyear.$baseno;
	}
}

	
	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcust'];
	$dDelDate = date("Y-m-d");
	$cRemarks = "NULL"; 
	//$cSalesType = $_REQUEST['seltype'];
	$cSalesType = "";
	$nGross = $_REQUEST['hdnItmTotAmt']; 
	$nLimit = $_REQUEST['ccustcredit']; 
	$nLimitBal = $_REQUEST['ccustbal']; 
	
	$nDue = $_REQUEST['GrandTot'];
	$nPayed = $_REQUEST['GrandPayed'];

	$chkCustAcct = mysqli_query($con,"select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'");

	if (!mysqli_query($con, "select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$AccntCode = $rowaccnt['cacctcodesales'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER
	//mysqli_query($con,"INSERT INTO sales(`compcode`, `csalesno`, `ccode`, `cremarks`, `csalestype`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`, `ndue`, `npayed`, `ncreditlimit`, `ncreditbal`) 
	//values('$company', '$cSINo', '$cCustID', $cRemarks, '$cSalesType', NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode', '$nDue', '$nPayed', '$nLimit', '$nLimitBal')");
	
	echo "INSERT INTO sales(`compcode`, `csalesno`, `ccode`, `cremarks`, `csalestype`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`, `ndue`, `npayed`, `ncreditlimit`, `ncreditbal`) 
	values('$company', '$cSINo', '$cCustID', $cRemarks, '$cSalesType', NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode', '$nDue', '$nPayed', '$nLimit', '$nLimitBal')";
	

	//INSERT DETAILS
	



	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','POS WIN','$compname','Inserted New Record')");

?>
<form action="index.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcsalesno" id="txtcsalesno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>