<?php
	if(!isset($_SESSION)){
	session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$cPVNo = $_REQUEST['txtctranno'];
	$company = $_SESSION['companyid'];

	@$arrwtxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `wtaxcodes` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrwtxlist[$row['ctaxcode']] = $row['nrate']; 
		}
	}

	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cAPtype =  mysqli_real_escape_string($con, $_REQUEST['selaptyp']); 
	//$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	//$cChkNo =  mysqli_real_escape_string($con, $_REQUEST['txtchkNo']);

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 

	$nGross =  mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnGross']));
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
		

	if (!mysqli_query($con, "UPDATE `apv` set `dapvdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ccode` = '$cCustID', `cpaymentfor` = '$cRemarks', `ngross` = '$nGross', `captype` = '$cAPtype', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate' Where `compcode` = '$company' and `ctranno` = '$cPVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
		
		//echo "<br> UPDATE `apv` set `dapvdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ccode` = '$cCustID', `cpaymentfor` = '$cRemarks', `ngross` = '$nGross', `captype` = '$cAPtype' Where `compcode` = '$company' and `ctranno` = '$cPVNo'";
	} 
	
	//INSERT WRR DETAILS

	if (!mysqli_query($con, "DELETE FROM `apv_d` Where `compcode` = '$company' and `ctranno` = '$cPVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$rowcnt = $_REQUEST['hdnRRCnt'];
		 
		 
	for($z=1; $z<=$rowcnt; $z++){

		//$suppsi = mysqli_real_escape_string($con, $_REQUEST['txtsuppSI'.$z]);
		//$cacctno = mysqli_real_escape_string($con, $_REQUEST['txtdracctid'.$z]);
		//$remarks = mysqli_real_escape_string($con, $_REQUEST['txtremarks'.$z]);
		//$desc= mysqli_real_escape_string($con, $_REQUEST['txtrrdesc'.$z]);
				
		$crrno = mysqli_real_escape_string($con, $_REQUEST['txtrefno'.$z]);	
		$ccustsi = mysqli_real_escape_string($con, $_REQUEST['txtrefsi'.$z]);	
		$acctno = mysqli_real_escape_string($con, $_REQUEST['txtrefacctno'.$z]);	
		$amnt = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnamount'.$z]));
		
		$vtcode = mysqli_real_escape_string($con, $_REQUEST['txtnvatcode'.$z]);
		$vtrate = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnvatrate'.$z]));
		$vtvals = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnvatval'.$z]));
		$vtnets = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtvatnet'.$z]));
		$ewtcde = mysqli_real_escape_string($con, $_REQUEST['txtewtcode'.$z]);
		$ewtrte = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtewtrate'.$z]));
		$ewtamt = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtewtamt'.$z]));
		//$paymnt = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtpayment'.$z]));
		$paymnt = 0;
		$dueamt = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtDue'.$z]));
		$applid = $dueamt;
		//$applid = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtnapplied'.$z]));
		$apcms = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtncm'.$z]));
		$apdiscs = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtndiscs'.$z]));
		
		$cacctno = "";

		$refcidenttran = $cPVNo."P".$z;
	
		if(!mysqli_query($con,"INSERT INTO `apv_d`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefno`, `crefinv`, `namount`, `cvatcode`, `nvatrate`, `nnet`, `nvatamt`, `cewtcode`, `newtrate`, `newtamt`, `napcm`, `napdisc`, `ndue`, `npayments`, `napplied`, `cacctno`) values('$company', '$refcidenttran', '$z', '$cPVNo', '$crrno', '$ccustsi', $amnt, '$vtcode', '$vtrate', $vtnets, $vtvals, '$ewtcde', '$ewtrte', $ewtamt, $apcms, $apdiscs, $dueamt, $paymnt, $applid, '$acctno')")){
			
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}

	//INSERT ACCNTS DETAILS

	if (!mysqli_query($con, "DELETE FROM `apv_t` Where `compcode` = '$company' and `ctranno` = '$cPVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$rowcnt = $_REQUEST['hdnACCCnt'];
		 
	for($z=1; $z<=$rowcnt; $z++){
		
		//$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcrefrr'.$z]);
		$crefrr = "";
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtacctno'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtacctitle'.$z]);
		$ndebit = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtdebit'.$z]));
		$ncredit = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtcredit'.$z]));
		//$nsubid = mysqli_real_escape_string($con,$_REQUEST['txtsubsid'.$z]);
		$cacctrem= mysqli_real_escape_string($con,$_REQUEST['txtacctrem'.$z]);
		$cacewtcode= mysqli_real_escape_string($con,$_REQUEST['txtewtcodeothers'.$z]); 
		$cacewteate = mysqli_real_escape_string($con, str_replace( ',', '', $_REQUEST['txtewtrateothers'.$z]));
	//	$cacctpaytyp= mysqli_real_escape_string($con,$_REQUEST['selacctpaytyp'.$z]);

		$refcidenttran = $cPVNo."P".$z;

		if($cacewtcode=="none"){
			$cacewtcode = "";
		}

		if($cacewteate==""){
			$cacewteate = @$arrwtxlist[$cacewtcode];
		}
		
		mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$z', '$cPVNo', '$crefrr', '$cacctno', '$ctitle', '$cacctrem', $ndebit, $ncredit, '$cacewtcode', $cacewteate)");

	}

	if (!mysqli_query($con, "DELETE FROM `apv_deds` Where `compcode` = '$company' and `ctranno` = '$cPVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}
	$rowcnt2 = $_REQUEST['hdnrowcnt2'];
	$zdc = 0;	 
	for($z=0; $z<=$rowcnt2-1; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcmrr'.$z]);
		$crefapcm = mysqli_real_escape_string($con,$_REQUEST['txtapcmdm'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtaccapcm'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdec'.$z]);
		$namt = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtapamt'.$z]));
		$apcmdte = mysqli_real_escape_string($con,$_REQUEST['txtapdte'.$z]);
		$cremrks = mysqli_real_escape_string($con,$_REQUEST['txtremz'.$z]);
		$wref = mysqli_real_escape_string($con,$_REQUEST['txtcmithref'.$z]);
		
		
		$refcidenttran = $cPVNo."P".$z;
		
		mysqli_query($con,"INSERT INTO `apv_deds`(`compcode`, `ctranno`, `cidentity`, `nidentity`, `cwithref`, `crefrr`, `crefapcm`, `drefapcmdate`, `ctype`, `namount`, `cremarks`, `cacctno`) values('$company', '$cPVNo', '$refcidenttran', '$z', $wref, '$crefrr', '$crefapcm', STR_TO_DATE('$apcmdte', '%m/%d/%Y'), 'CM', $namt, '$cremrks', '$cacctno')");
		
		$zdc = $z;

	}

	$rowcnt3 = $_REQUEST['hdnrowcnt3'];	 
	for($z=0; $z<=$rowcnt3-1; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcmdcrr'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdc'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdecdc'.$z]);
		$namt = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtapdcamt'.$z]));
		$cremrks = mysqli_real_escape_string($con,$_REQUEST['txtremzdc'.$z]);
		
		$zdc++;
		$refcidenttran = $cPVNo."P".$zdc;

		mysqli_query($con,"INSERT INTO `apv_deds`(`compcode`, `ctranno`, `cidentity`, `nidentity`, `crefrr`, `ctype`, `namount`, `cremarks`, `cacctno`) values('$company', '$cPVNo', '$refcidenttran', '$zdc', '$crefrr', 'DISC', $namt, '$cremrks', '$cacctno')");

	}


	//insert attachment
	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if(file_exists('../../Components/assets/APV/'.$company.'_'.$cPVNo.'/')) {
		/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
		$files = array_diff($allfiles, array('.', '..'));
		foreach($files as $file) {
			unlink("../../RFP_Files/".$cSINo."/".$file);
		}*/
	}else{
		if($total_count>=1){
			mkdir('../../Components/assets/APV/'.$company.'_'.$cPVNo.'/',0777);
		}
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/APV/" .$company.'_'. $cPVNo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cPVNo','$preparedby',NOW(),'UPDATED','APV','$compname','Update Record')");

?>
<form action="APV_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cPVNo;?>" />
</form>
<script>
	//alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>