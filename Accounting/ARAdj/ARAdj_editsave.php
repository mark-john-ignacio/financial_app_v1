<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');

$company = $_SESSION['companyid'];

	$cSINo =  mysqli_real_escape_string($con, $_REQUEST['txtctranno']);
	
	$dTranDate = $_REQUEST['date_delivery']; 
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']);
	$cSelType =  mysqli_real_escape_string($con, $_REQUEST['seltype']);   
	$cSRRef =  mysqli_real_escape_string($con, $_REQUEST['txtSIRef']);
	$cSIRef =  mysqli_real_escape_string($con, $_REQUEST['txtInvoiceRef']);
	$ngross =  mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnGross']));
	
	$dret = 0;
	if(isset($_REQUEST['isReturn'])){
		$dret = 1;
	}

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	
	if (!mysqli_query($con, "UPDATE `aradjustment` set `ccode` = '$cCustID', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ctype` = '$cSelType', `cremarks` = '$cRemarks', `ngross` = '$ngross', `crefsr` = '$cSRRef', `crefsi` = '$cSIRef', `isreturn` = '$dret' where `compcode`='$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


		//DELETE Details
		if (!mysqli_query($con, "UPDATE `aradjustment_t` set ctranno='xxx', compcode='xxx', cidentity=CONCAT(cidentity,'xxx') where `compcode`='$company' and `ctranno`= '$cSINo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		}
	
	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$isok = "YES";
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
	
		if (!mysqli_query($con,"INSERT INTO `aradjustment_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `cacctno`, `ctitle`, `ndebit`, `ncredit`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$cacctno', '$cacctdesc', $ndebit, $ncredit, $crem)")){
			$isok = "No";
		}

	}

	if($isok=="YES"){
		mysqli_query($con, "DELETE FROM `aradjustment_t` where `compcode`='xxx' and `ctranno`= 'xxx'");
	}
	

	//insert attachment
	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if(file_exists('../../Components/assets/ARAdj/'.$company.'_'.$cSINo.'/')) {
		/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {
			unlink("../../RFP_Files/".$cSINo."/".$file);
		}*/
	}else{
		if($total_count>=1){
			mkdir('../../Components/assets/ARAdj/'.$company.'_'.$cSINo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/ARAdj/" .$company.'_'. $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','AR ADJUSTMENT','$compname','Updated Record')");

?>
<form action="ARAdj_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
  document.forms['frmpos'].submit();
</script>