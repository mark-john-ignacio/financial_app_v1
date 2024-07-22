<?php

	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$dmonth = date("m");
	$dyear = date("y");
	$company = $_SESSION['companyid'];

	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";

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

	$chkSales = mysqli_query($con,"select * from paybill where compcode='$company' and YEAR(dtrandate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "PV".$dmonth.$dyear."00001";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "PV".$dmonth.$dyear."00001";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "PV".$dmonth.$dyear.$baseno;
		}
	}

	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";

		
	$cCustID = mysqli_real_escape_string($con, $_POST['txtcustid']);
	$cPayee = mysqli_real_escape_string($con, $_POST['txtpayee']);
	$cAcctNo = mysqli_real_escape_string($con, $_POST['txtcacctid']);
	$dDate = mysqli_real_escape_string($con, $_POST['date_delivery']);
	$nGross = mysqli_real_escape_string($con, $_POST['txtnGross']);

	$nGross = str_replace( ',', '', $nGross );


	$npaid = mysqli_real_escape_string($con, $_POST['txttotpaid']);	

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

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval'];

	$dret = 0;
	if(isset($_REQUEST['isNoRef'])){
		$dret = $_REQUEST['isNoRef'];
	}
	

	if (!mysqli_query($con, "INSERT INTO `paybill`(`compcode`, `ctranno`, `ccode`, `cpayee`, `cpaymethod`, `cbankcode`, `ccheckno`, `ccheckbook`, `cacctno`, `cpayrefno`, `ddate`, `dcheckdate`, `ngross`, `npaid`, `cpreparedby`, `cparticulars`, `cpaytype`, `lnoapvref`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`) values('$company', '$cSINo', '$cCustID', '$cPayee', '$paymeth', '$cBankCode', '$cCheckNo', '$cCheckBK', '$cAcctNo', '$cPayRefNo', STR_TO_DATE('$dDate', '%m/%d/%Y'), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), $nGross, $npaid, '$preparedby', '$particulars', '$paytype', $dret, '$CurrCode', '$CurrDesc', '$CurrRate')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	
	//INSERT APV DETAILS
	
	$rowcnt = $_POST['hdnrowcnt'];
	$cnt = 0;	 
	for($z=1; $z<=$rowcnt; $z++){ 
		
		
		$capvno = mysqli_real_escape_string($con, $_POST['cTranNo'.$z]);
		$crefrr = mysqli_real_escape_string($con, $_POST['cRefRRNo'.$z]);
		$dapvdate = $_POST['dApvDate'.$z];
		$namnt = mysqli_real_escape_string($con, $_POST['nAmount'.$z]);
		$namnt = str_replace( ',', '', $namnt );

		//$ndiscount = mysqli_real_escape_string($con, $_POST['nDiscount'.$z]);
		$ndiscount = 0;
		$nowed = mysqli_real_escape_string($con, $_POST['cTotOwed'.$z]);
		$nowed = str_replace( ',', '', $nowed );

		$napplied = mysqli_real_escape_string($con, $_POST['nApplied'.$z]);
		$napplied = str_replace( ',', '', $napplied );

		$caccno = mysqli_real_escape_string($con, $_POST['cacctno'.$z]); 

		if($_POST['isNoRef']==1){
			if($caccno==@$ewtpaydef || $caccno==@$OTpaydef){
				$hdnewt =$namnt; 
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
			
			$refcidenttran = $cSINo."P".$cnt;
		
			if($dapvdate==""){
				$dapvdate = date("m/d/Y");
			}

			if (!mysqli_query($con, "INSERT INTO `paybill_t`(`compcode`, `cidentity`, `nident`, `ctranno`, `crefrr`, `capvno`, `dapvdate`, `namount`, `ndiscount`, `nowed`, `napplied`, `cacctno`, `newtamt`, `cewtcode`, `entrytyp`, `ncostcenter`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$crefrr', '$capvno', STR_TO_DATE('$dapvdate', '%m/%d/%Y'), $namnt, $ndiscount, $nowed, $napplied, '$caccno', '$hdnewt', '$hdnewtcode', '$hdnentrtyp', '$selcostctr')")) {
			printf("Errormessage: %s\n", mysqli_error($con));
			} 
		
		}

	}

	$newchk = floatval($cCheckNo) + 1;
	mysqli_query($con,"UPDATE bank_check set ccurrentcheck='$newchk' where compcode='$company' and ccode='$cBankCode' and ccurrentcheck='$cCheckNo'");


	mysqli_query($con,"UPDATE bank_reserves set lused=1 where compcode='$company' and cbankcode='$cBankCode' and ccheckno='$cCheckNo'");
	

	//insert attachment

	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if($total_count>=1){
		mkdir('../../Components/assets/PV/'.$company.'_'.$cSINo.'/',0777);
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/PV/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}
	
	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','CHECK ISSUANCE','$compname','Inserted New Record')");

?>
<form action="th_acctentry2.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	//alert('Record Succesfully Saved');
  	document.forms['frmpos'].submit();
</script>