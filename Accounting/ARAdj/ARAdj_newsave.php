<?php
if(!isset($_SESSION)){
	session_start();
}
include('../../Connection/connection_string.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];

$chkSales = mysqli_query($con,"select * from aradjustment where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "AJ".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "AJ".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "AJ".$dmonth.$dyear.$baseno;
	}
}
	
	$dTranDate = $_REQUEST['date_delivery']; 
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']);
	$cSelType =  mysqli_real_escape_string($con, $_REQUEST['seltype']);   
	$cSRRef =  mysqli_real_escape_string($con, $_REQUEST['txtSIRef']);
	$cSIRef =  mysqli_real_escape_string($con, $_REQUEST['txtInvoiceRef']);
	$cCurrCode =  mysqli_real_escape_string($con, $_REQUEST['txtcurr']);
	$ngross =  mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnGross']));

	$dret = 0;
	if(isset($_REQUEST['isReturn'])){
		$dret = 1;
	}
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	

	if (!mysqli_query($con, "INSERT INTO `aradjustment`(`compcode`, `ctranno`, `ccode`, `ddate`, `dcutdate`, `ctype`, `cremarks`, `ngross`, `crefsr`, `crefsi`, `isreturn`,`cpreparedby`, `ccurrencycode`) values('$company', '$cSINo', '$cCustID', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cSelType', '$cRemarks', '$ngross', '$cSRRef', '$cSIRef', $dret, '$preparedby', '$cCurrCode')")) {
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
	
		mysqli_query($con,"INSERT INTO `aradjustment_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$cacctno', '$cacctdesc', $ndebit, $ncredit, $crem)");

	}
	

	//insert attachment

	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if($total_count>=1){
		mkdir('../../Components/assets/ARAdj/'.$company.'_'.$cSINo.'/',0777);
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/ARAdj/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','AR ADJUSTMENT','$compname','Inserted New Record')");

?>
<form action="ARAdj_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>