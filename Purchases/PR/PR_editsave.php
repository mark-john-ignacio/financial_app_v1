<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];
$preparedby = $_SESSION['employeeid'];

	$cSINo = mysqli_real_escape_string($con, $_REQUEST['txtcprno']);
	$cReqBy =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dDateNeed = $_REQUEST['date_needed'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cSection =  mysqli_real_escape_string($con, $_REQUEST['selwhfrom']);

	if (!mysqli_query($con, "UPDATE `purchrequest` set `locations_id` = '$cSection', `cremarks` = '$cRemarks', `dneeded` = STR_TO_DATE('$dDateNeed', '%m/%d/%Y') where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	//Savedetails
	if (!mysqli_query($con, "DELETE FROM `purchrequest_t` Where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
				
		$itmcode = mysqli_real_escape_string($con, $_REQUEST['txtitemcode'.$z]);
		$seluom = mysqli_real_escape_string($con, $_REQUEST['seluom'.$z]);		
		$nqty = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnqty'.$z]));		
		$nmainuom = mysqli_real_escape_string($con, $_REQUEST['hdnmainuom'.$z]);
		$nfactor = mysqli_real_escape_string($con, $_REQUEST['hdnfactor'.$z]);
		$drmrks = mysqli_real_escape_string($con, $_REQUEST['dremarks'.$z]);

		$refcidenttran = $cSINo."P".$z;
	
		if(!mysqli_query($con,"INSERT INTO `purchrequest_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `citemno`, `cunit`, `cmainunit`, `nfactor`, `nqty`, `cremarks`) values('$company', '$refcidenttran', '$z', '$cSINo', '$itmcode', '$seluom', '$nmainuom', '$nfactor', '$nqty', '$drmrks')")){ 
			
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}

	//insert attachment
	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if(file_exists('../../Components/assets/PReq/'.$company.'_'.$cSINo.'/')) {
		/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {
			unlink("../../RFP_Files/".$cSINo."/".$file);
		}*/
	}else{
		if($total_count>=1){
			mkdir('../../Components/assets/PReq/'.$company.'_'.$cSINo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/PReq/" .$company.'_'. $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}
		
	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE REQUEST','$compname','Inserted New Record')");

?>
<form action="PR_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
	<input type="hidden" name="hdnsrchval" id="hdnsrchval" value="<?=isset($_REQUEST['hdnsrchval']) ? $_REQUEST['hdnsrchval'] : ""?>" />
	<input type="hidden" name="hdnsrchsta" id="hdnsrchsta" value="<?=isset($_REQUEST['hdnsrchsta']) ? $_REQUEST['hdnsrchsta'] : ""?>" />
	<input type="hidden" name="hdnsrchsec" id="hdnsrchsec" value="<?=isset($_REQUEST['hdnsrchsec']) ? $_REQUEST['hdnsrchsec'] : ""?>" />
	<input type="hidden" name="hdnsrchdte" id="hdnsrchdte" value="<?=isset($_REQUEST['hdnsrchdte']) ? $_REQUEST['hdnsrchdte'] : ""?>" />
	<input type="hidden" name="hdnsrchdtef" id="hdnsrchdtef" value="<?=isset($_REQUEST['hdnsrchdtef']) ? $_REQUEST['hdnsrchdtef'] : ""?>" />
	<input type="hidden" name="hdnsrchdtet" id="hdnsrchdtet" value="<?=isset($_REQUEST['hdnsrchdtet']) ? $_REQUEST['hdnsrchdtet'] : ""?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>