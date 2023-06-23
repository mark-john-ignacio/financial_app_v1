<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$dmonth = date("m");
	$dyear = date("y");
	$company = $_SESSION['companyid'];


	$cSINo = $_POST['txtctranno'];

	$cCustID = mysqli_real_escape_string($con, $_POST['txtcustid']);
	$cRefAPV = mysqli_real_escape_string($con, $_POST['txtrefapv']);
	$dDate = mysqli_real_escape_string($con, $_POST['txtChekDate']);
	$paymeth = mysqli_real_escape_string($con, $_POST['selpayment']); 
	$cBankCode = mysqli_real_escape_string($con, $_POST['txtBank']);

	$npaid = mysqli_real_escape_string($con, $_POST['txtnamount']);	
	$npaid = str_replace( ',', '', $npaid );

	$nbalance = mysqli_real_escape_string($con, $_POST['txtnamountbal']);	
	$nbalance = str_replace( ',', '', $nbalance );

	$cremarks = mysqli_real_escape_string($con, $_POST['txtcremarks']);

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);


	if (!mysqli_query($con, "UPDATE `rfp` set `ccode` = '$cCustID', `cpaymethod` = '$paymeth', `cbankcode` = '$cBankCode', `capvno` = '$cRefAPV', `dtransdate` = STR_TO_DATE('$dDate', '%m/%d/%Y'), `ngross` = $npaid, `nbalance` = $nbalance, `cremarks` = '$cremarks' where`compcode` = '$company' and `ctranno` = '$cSINo'")) {
		$mggx = "Errormessage: ". mysqli_error($con);
	} else{
		$mggx = "Record Succesfully Saved";

		//insert attachment
			$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
			// Count the number of uploaded files in array
			$total_count = count($_FILES['upload']['name']);

			if(file_exists('../../RFP_Files/'.$company.'_'.$cSINo.'/')) {
				/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
				$files = array_diff($allfiles, array('.', '..'));
				foreach($files as $file) {
					unlink("../../RFP_Files/".$cSINo."/".$file);
				}*/
			}else{
				if($total_count>=1){
					mkdir('../../RFP_Files/'.$company.'_'.$cSINo.'/',0777);
				}
			}

			// Loop through every file
			for( $i=0 ; $i < $total_count ; $i++ ) {
				//The temp file path is obtained
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
				//A file path needs to be present
				if ($tmpFilePath != ""){
						//Setup our new file path
						$newFilePath = "../../RFP_Files/" .$company.'_'. $cSINo . "/" . $_FILES['upload']['name'][$i];
						//File is uploaded to temp dir
						move_uploaded_file($tmpFilePath, $newFilePath);
						
				}
			}

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'UPDATE','RFP','$compname','Update Record')");

	}
	

?>
<form action="RFP_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('<?=$mggx?>');
  document.forms['frmpos'].submit();
</script>