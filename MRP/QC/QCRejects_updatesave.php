<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	$cSINo = $_POST['ctranno'];
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);


	if($total_count>=1 && $_FILES['upload']['name'][0] !=""){
		if (!file_exists('../../Components/assets/QCRej/'.$company.'_'.$cSINo)) {
			mkdir('../../Components/assets/QCRej/'.$company.'_'.$cSINo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/QCRej/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}

	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','QC REJECTS','$compname','Updated Attachments')");

?>
<form action="QCRejects_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?=$cSINo;?>" />
</form>
<script>

	alert('Attachments Succesfully Saved');

    document.forms['frmpos'].submit();
</script>