<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

	//$dmonth = date("m");
	$cCVNo = $_REQUEST['txtctranno'];
	$company = $_SESSION['companyid'];

	//get default EWT acct code
	@$ewtpaydef = "";
	$gettaxcd = mysqli_query($con,"SELECT * FROM `accounts_default` where compcode='$company' and ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno']; 
		}
	}

	//get default Input tax acct code
	@$OTpaydef = "";
	$gettaxcd = mysqli_query($con,"SELECT * FROM `accounts_default` where compcode='$company' and ccode='PURCH_VAT'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$OTpaydef = $row['cacctno']; 
		}
	}

	//echo "<pre>";
	//print_r($_REQUEST);
	//echo "</pre>";

	$cCustID = mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$cPayee = mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	$cAcctNo = mysqli_real_escape_string($con, $_REQUEST['txtcacctid']);
	$dDate = mysqli_real_escape_string($con, $_REQUEST['date_delivery']);
	$nGross = mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$nGross = str_replace( ',', '', $nGross );

	$npaid = mysqli_real_escape_string($con, $_REQUEST['txttotpaid']);
	$npaid = str_replace( ',', '', $npaid );

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	$paymeth = mysqli_real_escape_string($con, $_POST['selpayment']);
	//$paytype = mysqli_real_escape_string($con, $_POST['selpaytype']); 
	$paytype = "apv";
	$particulars = mysqli_real_escape_string($con, $_POST['txtparticulars']);

	if($paymeth=="cash"){
		$dTranDate = mysqli_real_escape_string($con, $dDate);
	}else{
		$dTranDate = mysqli_real_escape_string($con, $_POST['txtChekDate']);
	}

	if($paymeth=="cheque"){
		$cBankCode = mysqli_real_escape_string($con, $_POST['txtBank']);
		$cCheckNo = mysqli_real_escape_string($con, $_POST['txtCheckNo']);			
		$cCheckBK = mysqli_real_escape_string($con, $_POST['txtChkBkNo']);

		$cPayRefNo = "";
	}else{
		$cBankCode = mysqli_real_escape_string($con, $_POST['txtBank']);
		$cCheckNo = "";	
		$cCheckBK = "";

		$cPayRefNo = mysqli_real_escape_string($con, $_POST['txtPayRefrnce']);
	}
	
	$dret = 0;
	if(isset($_REQUEST['isNoRef'])){
		$dret = $_REQUEST['isNoRef'];
	}

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval'];

	if (!mysqli_query($con, "UPDATE `paybill` set `dcheckdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ccode` = '$cCustID', `cpayee` = '$cPayee', `ngross` = $nGross, `npaid` = $npaid, `cacctno` = '$cAcctNo', ddate = STR_TO_DATE('$dDate', '%m/%d/%Y'), dcheckdate = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cbankcode` = '$cBankCode', `ccheckno` = '$cCheckNo', `ccheckbook` = '$cCheckBK', `cpaymethod` = '$paymeth', `cpayrefno` = '$cPayRefNo', `cparticulars` = '$particulars', `cpaytype` = '$paytype', `lnoapvref` = $dret, `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate' where `compcode` = '$company' and `ctranno` = '$cCVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 



	//INSERT WRR DETAILS

	if (!mysqli_query($con, "DELETE FROM `paybill_t` Where `compcode` = '$company' and `ctranno` = '$cCVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){ 
		
		$capvno = mysqli_real_escape_string($con, $_REQUEST['cTranNo'.$z]);
		$crefrr = mysqli_real_escape_string($con, $_POST['cRefRRNo'.$z]);
		$dapvdate = $_REQUEST['dApvDate'.$z];
		$namnt = mysqli_real_escape_string($con, $_REQUEST['nAmount'.$z]);
		$namnt = str_replace( ',', '', $namnt );
		
		//$ndiscount = mysqli_real_escape_string($con, $_REQUEST['nDiscount'.$z]);
		$ndiscount = 0;
		$nowed = mysqli_real_escape_string($con, $_REQUEST['cTotOwed'.$z]);
		$nowed = str_replace( ',', '', $nowed );

		$napplied = mysqli_real_escape_string($con, $_REQUEST['nApplied'.$z]);
		$napplied = str_replace( ',', '', $napplied );

		$caccno = mysqli_real_escape_string($con, $_REQUEST['cacctno'.$z]); 
		
		if($_POST['isNoRef']==1){
			if($caccno==@$ewtpaydef || $caccno==@$OTpaydef){
				$hdnewt = $namnt; 
				$hdnewtcode = mysqli_real_escape_string($con, $_POST['napvewt'.$z]);
			}else{
				$hdnewt = 0; 
				$hdnewtcode = "";
			}
		}else{
			$hdnewt = mysqli_real_escape_string($con, $_POST['napvewt'.$z]); 
			$hdnewtcode = "";
		}
		 
		$hdnentrtyp = mysqli_real_escape_string($con, $_POST['selentrytyp'.$z]);
		$selcostctr = mysqli_real_escape_string($con, $_POST['selcostcentr'.$z]); 

		if($napplied<>0){
			
			$cnt = $cnt + 1;		
			$refcidenttran = $cCVNo."P".$cnt;
		
			if($dapvdate==""){
				$dapvdate = date("m/d/Y");
			}
			
			if (!mysqli_query($con, "INSERT INTO `paybill_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `crefrr`, `capvno`, `dapvdate`, `namount`, `ndiscount`, `nowed`, `napplied`, `cacctno`, `newtamt`, `cewtcode`, `entrytyp`, `ncostcenter`) values('$company', '$refcidenttran', '$cnt', '$cCVNo', '$crefrr', '$capvno', STR_TO_DATE('$dapvdate', '%m/%d/%Y'), $namnt, $ndiscount, $nowed, $napplied, '$caccno', $hdnewt, '$hdnewtcode', '$hdnentrtyp', '$selcostctr')")) {
			printf("Errormessage: %s\n", mysqli_error($con));
			} 

		
		}

	}

	//pag same sa currentchck ung andito.. add 1
	$ccurchk = "";
	$ccurchklast = "";
	$ccurchkbk = "";
	$sql = mysqli_query($con,"Select * from bank_check where compcode='$company' and ccode='$cBankCode' and ccheckto <> ccurrentcheck"); 
	while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC))
	{
		$ccurchk = $row['ccurrentcheck'];
		$ccurchklast = $row['ccheckto'];
		$ccurchkbk = $row['ccheckno'];
	}

	if(floatval($cCheckNo)==floatval($ccurchk) && floatval($ccurchk) < floatval($ccurchklast)){
		$newchk = floatval($cCheckNo) + 1;
		mysqli_query($con,"UPDATE bank_check set ccurrentcheck='$newchk' where compcode='$company' and ccode='$cBankCode' and ccheckno='$ccurchkbk'");
	}

	mysqli_query($con,"UPDATE bank_reserves set lused=1 where compcode='$company' and cbankcode='$cBankCode' and ccheckno='$cCheckNo'");
	

	//insert attachment
	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if(file_exists('../../Components/assets/PV/'.$company.'_'.$cCVNo.'/')) {
		/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {
			unlink("../../RFP_Files/".$cSINo."/".$file);
		}*/
	}else{
		if($total_count>=1){
			mkdir('../../Components/assets/PV/'.$company.'_'.$cCVNo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/PV/" .$company.'_'. $cCVNo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cCVNo','$preparedby',NOW(),'UPDATED','CHECK ISSUANCE','$compname','Updated Record')");


?>
<form action="th_acctentry2.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cCVNo;?>" />
</form>
<script>
	//alert('Record Succesfully Updated');
  	document.forms['frmpos'].submit();
</script>