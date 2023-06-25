<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$dmonth = date("m");
	$dyear = date("y");
	$company = $_SESSION['companyid'];

	$chkSales = mysqli_query($con,"select * from rfp where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "RP".$dmonth.$dyear."00001";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "RP".$dmonth.$dyear."00001";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "RP".$dmonth.$dyear.$baseno;
		}
	}

	
	$cCustID = mysqli_real_escape_string($con, $_POST['txtcustid']);
	$cRefAPV = mysqli_real_escape_string($con, $_POST['txtrefapv']);
	$dDate = mysqli_real_escape_string($con, $_POST['txtChekDate']);
	$paymeth = mysqli_real_escape_string($con, $_POST['selpayment']); 
	$cBankCode = mysqli_real_escape_string($con, $_POST['txtBank']);

	$cAcctCode = mysqli_real_escape_string($con, $_POST['txtcacctnoref']);

	$npaid = mysqli_real_escape_string($con, $_POST['txtnamount']);	
	$npaid = str_replace( ',', '', $npaid );

	$nbalance = mysqli_real_escape_string($con, $_POST['txtnamountbal']);	
	$nbalance = str_replace( ',', '', $nbalance );

	$cremarks = mysqli_real_escape_string($con, $_POST['txtcremarks']);

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);


	if (!mysqli_query($con, "INSERT INTO `rfp`(`compcode`, `ctranno`, `ccode`, `cpaymethod`, `cbankcode`, `capvno`, `cacctno`, `ngross`, `nbalance`, `dtransdate`, `cpreparedby`, `cremarks`) values('$company', '$cSINo', '$cCustID', '$paymeth', '$cBankCode', '$cRefAPV', '$cAcctCode', $npaid, $nbalance, STR_TO_DATE('$dDate', '%m/%d/%Y'), '$preparedby', '$cremarks')")) {
		$mggx = "Errormessage: ". mysqli_error($con);
	} else{
		$mggx = "Record Succesfully Saved";

		//insert attachment

			$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
			// Count the number of uploaded files in array
			$total_count = count($_FILES['upload']['name']);

			if($total_count>=1){
				mkdir('../../RFP_Files/'.$company.'_'.$cSINo.'/',0777);
			}

			// Loop through every file
			for( $i=0 ; $i < $total_count ; $i++ ) {
				//The temp file path is obtained
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
				//A file path needs to be present
				if ($tmpFilePath != ""){
						//Setup our new file path
						$newFilePath = "../../RFP_Files/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
						//File is uploaded to temp dir
						move_uploaded_file($tmpFilePath, $newFilePath);
						
				}
			}

		//INSERT LOGFILE
		$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			
		mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
		values('$company','$cSINo','$preparedby',NOW(),'INSERTED','RFP','$compname','Inserted New Record')");

	}
	

?>
<form action="RFP_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('<?=$mggx?>');
  document.forms['frmpos'].submit();
</script>