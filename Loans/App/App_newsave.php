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


$chkSales = mysqli_query($con,"select * from loans where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "LO".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>"; 2016-01-0001;
	//echo substr($lastSI,5,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "LO".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "LO".$dmonth.$dyear.$baseno;
	}
}

	
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

	
	if (!mysqli_query($con, "INSERT INTO `loans`(`compcode`, `ctranno`, `ccode`, `cyrs`, `cdeptid`, `nmembertype`, `ncapshare`, `cpurpose`, `ddate`, `dbegin`, `dend`, `cloantype`, `cpaytype`, `cterms`, `nintrate`, `nloaned`, `namount`, `naddfee`, `ntotint`, `npayamt`, `ndedamt`,  `cdedtype`, `lautoded`) VALUES ('$company','$cSINo','$cCode','$nyrs','$cDeptID','$cMemType','$ncapshare','$cpurpose',NOW(), STR_TO_DATE('$dbegin', '%m/%d/%Y'), STR_TO_DATE('$dend', '%m/%d/%Y'),'$cLoanType','$cPayType','$cTerms','$nIntRate','$nLoanAmt','$nAmount','$nAddFee','$nTotInt','$nTotAmtLoan',$nDedAmt,'$hdnDedType',$autoded)")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','LOAN APP','$compname','Inserted New Record')");

?>
<form action="App_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>