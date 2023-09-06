<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$company = $_SESSION['companyid'];
$cSINo =  mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cTotDeb =  mysqli_real_escape_string($con, $_REQUEST['txtnDebit']);
	$cTotCrd =  mysqli_real_escape_string($con, $_REQUEST['txtnCredit']);

	$cTotDeb = str_replace( ',', '', $cTotDeb );
	$cTotCrd = str_replace( ',', '', $cTotCrd );

	//$cTotTax =  mysqli_real_escape_string($con, $_REQUEST['txtnTax']);
	$cTotTax = 0;
	
	//if($_REQUEST['lTaxInc']=="YES"){ 
	//	$lTaxInc = 1;
	//}
	//else{
		$lTaxInc = 0;
	//}
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	if (!mysqli_query($con, "UPDATE `journal` set `djdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cmemo` = '$cRemarks',  `ltaxinc` = $lTaxInc,  `ntotdebit`=$cTotDeb, `ntotcredit`=$cTotCrd, `ntottax`=$cTotTax where `compcode`='$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


		//DELETE Details
		if (!mysqli_query($con, "DELETE FROM `journal_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		}
	
	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnACCCnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cacctno = mysqli_real_escape_string($con, $_REQUEST['txtcAcctNo'.$z]);
		$cacctdesc = mysqli_real_escape_string($con, $_REQUEST['txtcAcctDesc'.$z]);
		$ndebit= mysqli_real_escape_string($con, $_REQUEST['txtnDebit'.$z]);
		$ncredit = mysqli_real_escape_string($con, $_REQUEST['txtnCredit'.$z]);
		$nsub = mysqli_real_escape_string($con, $_REQUEST['txtnSub'.$z]);
		$crem = mysqli_real_escape_string($con, $_REQUEST['txtcRem'.$z]);

		$ndebit = str_replace( ',', '', $ndebit );
		$ncredit = str_replace( ',', '', $ncredit );
		
		if($nsub==""){
			$nsub = "NULL";
		}else{
			$nsub = "'".$nsub."'";
		}
		
		if($crem==""){
			$crem = "NULL";
		}else{
			$crem = "'".$crem."'";
		}

		$refcidenttran = $cSINo."P".$z;
	
		mysqli_query($con,"INSERT INTO `journal_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`, `csub`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$cacctno', '$cacctdesc', $ndebit, $ncredit, $nsub, $crem)");

	}


	//insert attachment
	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if(file_exists('../Components/assets/Journal/'.$company.'_'.$cSINo.'/')) {
		/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {
			unlink("../../RFP_Files/".$cSINo."/".$file);
		}*/
	}else{
		if($total_count>=1){
			mkdir('../Components/assets/Journal/'.$company.'_'.$cSINo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../Components/assets/Journal/" .$company.'_'. $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','JOURNAL','$compname','Updated Record')");

?>
<form action="Journal_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>