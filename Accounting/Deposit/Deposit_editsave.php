<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');


$company = $_SESSION['companyid'];


	$cSINo = mysqli_real_escape_string($con, $_REQUEST['txtctranno']);

	$cBankCode =  mysqli_real_escape_string($con, $_REQUEST['selbanks']);
	$cReference =  mysqli_real_escape_string($con, $_REQUEST['txtrefno']);
	$cAcctNo =  mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayMethod =  "";
	
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$nGross = str_replace(",","",$nGross);
	
	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate = $_REQUEST['basecurrval'];
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	
	if (!mysqli_query($con, "UPDATE `deposit` set `cortype` = '$cPayMethod', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cremarks` = '$cRemarks', `cacctcode` = '$cAcctNo', `namount` = $nGross, `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `cbankcode` = '$cBankCode', `creference` = '$cReference' where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	
	if (!mysqli_query($con, "DELETE FROM `deposit_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}


	//INSERT SALES DETAILS if Sales and Sales Type
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$csalesno = mysqli_real_escape_string($con, $_REQUEST['txtcSalesNo'.$z]);
		$crefso = mysqli_real_escape_string($con, $_REQUEST['txtcReference'.$z]);
		$namount = $_REQUEST['txtnAmt'.$z];
				
		$cnt = $cnt + 1;
		
		 $refcidenttran = $cSINo."P".$cnt;

			if (!mysqli_query($con, "INSERT INTO `deposit_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `corno`, `creference`, `namount`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno', '$crefso', $namount)")) {
				//printf("INSERT INTO `deposit_t`(`compcode`, `ctranno`, `corno`) values('$company', '$cSINo', '$csalesno')\n");
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	

	//insert attachment
	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if(file_exists('../../Components/assets/Deposit/'.$company.'_'.$cSINo.'/')) {
		/*$allfiles = scandir('../../../../RFP_Files/'.$cSINo.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {
			unlink("../../../../RFP_Files/".$cSINo."/".$file);
		}*/
	}else{
		if($total_count>=1){
			mkdir('../../Components/assets/Deposit/'.$company.'_'.$cSINo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/Deposit/" .$company.'_'. $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','BANK DEPOSIT','$compname','Updated Record')");

?>
<form action="Deposit_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>