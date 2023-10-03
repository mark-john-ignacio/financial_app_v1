<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

//$dmonth = date("m");
$dyear = date("y");

$dmonth = "01";
//$dyear = "16";

$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from deposit where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "BD".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>"; 2016-01-0001;
	//echo substr($lastSI,5,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "BD".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "BD".$dmonth.$dyear.$baseno;
	}
}

	
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayMethod =  "";
	
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$nGross = str_replace(",","",$nGross);
	
	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval'];

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	if (!mysqli_query($con, "INSERT INTO `deposit`(`compcode`, `ctranno`, `cortype`, `ddate`, `dcutdate`, `cremarks`, `cacctcode`, `cpreparedby`, `namount`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`) values('$company', '$cSINo', '$cPayMethod', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cRemarks', '$cAcctNo', '$preparedby', $nGross, '$CurrCode', '$CurrDesc', '$CurrRate')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
//INSERT SALES DETAILS if Sales and Sales Type
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$csalesno = mysqli_real_escape_string($con, $_REQUEST['txtcSalesNo'.$z]);
				
		$cnt = $cnt + 1;
		
		 $refcidenttran = $cSINo."P".$cnt;

			if (!mysqli_query($con, "INSERT INTO `deposit_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `corno`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno')")) {
				//printf("INSERT INTO `deposit_t`(`compcode`, `ctranno`, `corno`) values('$company', '$cSINo', '$csalesno')\n");
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	

	//insert attachment

	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if($total_count>=1){
		mkdir('../../Components/assets/Deposit/'.$company.'_'.$cSINo.'/',0777);
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/Deposit/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','BANK DEPOSIT','$compname','Inserted New Record')");

?>
<form action="Deposit_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>