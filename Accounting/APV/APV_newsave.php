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
//print_r($_REQUEST);
//echo "</pre>";

	@$arrwtxlist = array();
	$gettaxcd = mysqli_query($con,"SELECT * FROM `wtaxcodes` where compcode='$company' and cstatus='ACTIVE'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$arrwtxlist[$row['ctaxcode']] = $row['nrate']; 
		}
	}




$chkSales = mysqli_query($con,"select * from apv where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "AP".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "AP".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "AP".$dmonth.$dyear.$baseno;
	}
}

	
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	$cAPtype =  mysqli_real_escape_string($con, $_REQUEST['selaptyp']);

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	$nGross = str_replace( ',', '', $nGross );


	if (!mysqli_query($con, "INSERT INTO `apv`(`compcode`, `ctranno`, `ddate`, `dapvdate`, `ccode`, `cpayee`, `cpaymentfor`, `ngross`, `cpreparedby`, `captype`, `ccurrencycode`, `ccurrencydesc`, `nexchangerate`) values('$company', '$cSINo', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), '$cCustID', '$cPayee','$cRemarks', $nGross, '$preparedby', '$cAPtype', '$CurrCode', '$CurrDesc', '$CurrRate')")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	//INSERT WRR DETAILS
	
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
		$vtrate = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtnvatrate'.$z]));
		$vtvals = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtnvatval'.$z]));
		$vtnets = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtvatnet'.$z]));
		$ewtcde = mysqli_real_escape_string($con, $_REQUEST['txtewtcode'.$z]);
		$ewtrte = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtewtrate'.$z]));
		$ewtamt = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtewtamt'.$z]));
		//$paymnt = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtpayment'.$z]));
		$paymnt = 0;
		$dueamt = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtDue'.$z]));
		$applid = $dueamt;
		//$applid = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtnapplied'.$z]));  
		$apcms = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtncm'.$z]));
		$apdiscs = mysqli_real_escape_string($con,  str_replace( ',', '', $_REQUEST['txtndiscs'.$z]));
		
		$cacctno = "";

		$refcidenttran = $cSINo."P".$z;
	
		if(!mysqli_query($con,"INSERT INTO `apv_d`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefno`, `crefinv`, `namount`, `cvatcode`, `nvatrate`, `nnet`, `nvatamt`, `cewtcode`, `newtrate`, `newtamt`, `napcm`, `napdisc`, `ndue`, `npayments`, `napplied`, `cacctno`) values('$company', '$refcidenttran', '$z', '$cSINo', '$crrno', '$ccustsi', $amnt, '$vtcode', '$vtrate', $vtnets, $vtvals, '$ewtcde', '$ewtrte', $ewtamt, $apcms, $apdiscs, $dueamt, $paymnt, $applid, '$acctno')")){ 
			
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}

	//INSERT ACCNTS DETAILS
	
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
		//$cacctpaytyp= mysqli_real_escape_string($con,$_REQUEST['selacctpaytyp'.$z]);
		
		$refcidenttran = $cSINo."P".$z;

		//echo "INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`) values('$company', '$refcidenttran', '$z', '$cSINo', '$crefrr', '$cacctno', '$ctitle', '$cacctrem', $ndebit, $ncredit)";

		if($cacewtcode=="none"){
			$cacewtcode = "";
		}

		if($cacewteate==""){
			$cacewteate = @$arrwtxlist[$cacewtcode];
		}
		
		mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$z', '$cSINo', '$crefrr', '$cacctno', '$ctitle', '$cacctrem', $ndebit, $ncredit, '$cacewtcode', $cacewteate)");

	}

	$rowcnt2 = $_REQUEST['hdnrowcnt2'];
	$zdc = 0;	 
	for($z=1; $z<=$rowcnt2; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcmrr'.$z]);
		$crefapcm = mysqli_real_escape_string($con,$_REQUEST['txtapcmdm'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtaccapcm'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdec'.$z]);
		$namt = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtapamt'.$z]));
		$apcmdte = mysqli_real_escape_string($con,$_REQUEST['txtapdte'.$z]);
		$cremrks = mysqli_real_escape_string($con,$_REQUEST['txtremz'.$z]);
		$wref = mysqli_real_escape_string($con,$_REQUEST['txtcmithref'.$z]);
		
		
		$refcidenttran = $cSINo."P".$z;
		
		mysqli_query($con,"INSERT INTO `apv_deds`(`compcode`, `ctranno`, `cidentity`, `nidentity`, `cwithref`, `crefrr`, `crefapcm`, `drefapcmdate`, `ctype`, `namount`, `cremarks`, `cacctno`) values('$company', '$cSINo', '$refcidenttran', '$z', $wref, '$crefrr', '$crefapcm', STR_TO_DATE('$apcmdte', '%m/%d/%Y'), 'CM', $namt, '$cremrks', '$cacctno')");
		
		$zdc = $z;

	}

	$rowcnt3 = $_REQUEST['hdnrowcnt3'];	 
	for($z=1; $z<=$rowcnt3; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcmdcrr'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdc'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdecdc'.$z]);
		$namt = mysqli_real_escape_string($con,str_replace( ',', '', $_REQUEST['txtapdcamt'.$z]));
		$cremrks = mysqli_real_escape_string($con,$_REQUEST['txtremzdc'.$z]);
		
		$zdc++;
		$refcidenttran = $cSINo."P".$zdc;
		
		mysqli_query($con,"INSERT INTO `apv_deds`(`compcode`, `ctranno`, `cidentity`, `nidentity`, `crefrr`, `ctype`, `namount`, `cremarks`, `cacctno`) values('$company', '$cSINo', '$refcidenttran', '$zdc', '$crefrr', 'DISC', $namt, '$cremrks', '$cacctno')");

	}


	//insert attachment

	$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
	// Count the number of uploaded files in array
	$total_count = count($_FILES['upload']['name']);

	if($total_count>=1){
		mkdir('../../Components/assets/APV/'.$company.'_'.$cSINo.'/',0777);
	}

	// Loop through every file
	for( $i=0 ; $i < $total_count ; $i++ ) {
		//The temp file path is obtained
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
		//A file path needs to be present
		if ($tmpFilePath != ""){
				//Setup our new file path
				$newFilePath = "../../Components/assets/APV/".$company.'_' . $cSINo . "/" . $_FILES['upload']['name'][$i];
				//File is uploaded to temp dir
				move_uploaded_file($tmpFilePath, $newFilePath);
				
		}
	}

		
	//INSERT LOGFILE
	$compname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','APV','$compname','Inserted New Record')");

?>
<form action="APV_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>