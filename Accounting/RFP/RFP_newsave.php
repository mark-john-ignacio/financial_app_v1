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
	//$cRefAPV = mysqli_real_escape_string($con, $_POST['txtrefapv']);
	$dDate = mysqli_real_escape_string($con, $_POST['txtChekDate']);
	$paymeth = mysqli_real_escape_string($con, $_POST['selpayment']); 

	$cBankName = mysqli_real_escape_string($con, $_POST['txtBankName']);
	$cBankAcctNo = mysqli_real_escape_string($con, $_POST['txtAcctNo']);
	$cBankAcctNm = mysqli_real_escape_string($con, $_POST['txtAcctName']);

//	$cAcctCode = mysqli_real_escape_string($con, $_POST['txtcacctnoref']);

	$npaid = mysqli_real_escape_string($con, $_POST['txtnamount']);	
	$npaid = str_replace( ',', '', $npaid );

	//$nbalance = mysqli_real_escape_string($con, $_POST['txtnamountbal']);	
//	$nbalance = str_replace( ',', '', $nbalance );

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 

	$cremarks = mysqli_real_escape_string($con, $_POST['txtcremarks']);
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);


	if (!mysqli_query($con, "INSERT INTO `rfp`(`compcode`, `ctranno`, `ccode`, `cpaymethod`, `cbankname`, `cbankacctno`, `cbankacctname`, `ngross`, `dtransdate`, `cpreparedby`, `cremarks`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`) values('$company', '$cSINo', '$cCustID', '$paymeth', '$cBankName', '$cBankAcctNo', '$cBankAcctNm', $npaid, STR_TO_DATE('$dDate', '%m/%d/%Y'), '$preparedby', '$cremarks', '$CurrCode', '$CurrDesc', '$CurrRate')")) {
		$mggx = "Errormessage: ". mysqli_error($con);
	} else{

		//Savedetails
		$rowcnt = $_REQUEST['hdndetails'];		 
		for($z=1; $z<=$rowcnt; $z++){
			$capvno = mysqli_real_escape_string($con, $_REQUEST['txtcapvno'.$z]);	
			$capvdate = mysqli_real_escape_string($con, $_REQUEST['txtcapvdate'.$z]);		
			$acctno = mysqli_real_escape_string($con, $_REQUEST['txtapvacctid'.$z]); 
			$acctdesc = mysqli_real_escape_string($con, $_REQUEST['txtapvacctitle'.$z]);
			$amnttot = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtapvamt'.$z]));
			$amtbal = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtapvbal'.$z]));

			mysqli_query($con, "INSERT INTO `rfp_t`(`compcode`, `ctranno`, `capvno`, `dapvdate`, `cacctno`, `cacctdesc`, `ngrossamt`, `npayable`) values('$company', '$cSINo', '$capvno', '$capvdate', '$acctno', '$acctdesc', $amnttot, $amtbal)");
		}


		$mggx = "Record Succesfully Saved";

		//insert attachment

			$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
			// Count the number of uploaded files in array
			$total_count = count($_FILES['upload']['name']);

			if($total_count>=1){
				mkdir('../../Components/assets/RFP/'.$company.'_'.$cSINo.'/',0777);
			}

			// Loop through every file
			for( $i=0 ; $i < $total_count ; $i++ ) {
				//The temp file path is obtained
				$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
				//A file path needs to be present
				if ($tmpFilePath != ""){
						//Setup our new file path
						$newFilePath = "../../Components/assets/RFP/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
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