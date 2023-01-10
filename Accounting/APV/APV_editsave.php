<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$cPVNo = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];

	
	$cCustID =  mysqli_real_escape_string($con, $_REQUEST['txtcustid']);
	$dTranDate = $_REQUEST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_REQUEST['txtremarks']); 
	$cAPtype =  mysqli_real_escape_string($con, $_REQUEST['selaptyp']); 
	//$cPayee =  mysqli_real_escape_string($con, $_REQUEST['txtpayee']);
	//$cChkNo =  mysqli_real_escape_string($con, $_REQUEST['txtchkNo']);
	$nGross =  mysqli_real_escape_string($con, $_REQUEST['txtnGross']);
	
	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
		

	if (!mysqli_query($con, "UPDATE `apv` set `dapvdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ccode` = '$cCustID', `cpaymentfor` = '$cRemarks', `ngross` = '$nGross', `captype` = '$cAPtype' Where `compcode` = '$company' and `ctranno` = '$cPVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
		
		echo "<br> UPDATE `apv` set `dapvdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `ccode` = '$cCustID', `cpaymentfor` = '$cRemarks', `ngross` = '$nGross', `captype` = '$cAPtype' Where `compcode` = '$company' and `ctranno` = '$cPVNo'";
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
		$amnt = mysqli_real_escape_string($con, $_REQUEST['txtnamount'.$z]);
		
		$vtcode = mysqli_real_escape_string($con, $_REQUEST['txtnvatcode'.$z]);
		$vtrate = mysqli_real_escape_string($con, $_REQUEST['txtnvatrate'.$z]);
		$vtvals = mysqli_real_escape_string($con, $_REQUEST['txtnvatval'.$z]);
		$vtnets = mysqli_real_escape_string($con, $_REQUEST['txtvatnet'.$z]);
		$ewtcde = mysqli_real_escape_string($con, $_REQUEST['txtewtcode'.$z]);
		$ewtrte = mysqli_real_escape_string($con, $_REQUEST['txtewtrate'.$z]);
		$ewtamt = mysqli_real_escape_string($con, $_REQUEST['txtewtamt'.$z]);
		$paymnt = mysqli_real_escape_string($con, $_REQUEST['txtpayment'.$z]);
		$dueamt = mysqli_real_escape_string($con, $_REQUEST['txtDue'.$z]);
		$applid = mysqli_real_escape_string($con, $_REQUEST['txtnapplied'.$z]);
		$apcms = mysqli_real_escape_string($con, $_REQUEST['txtncm'.$z]);
		$apdiscs = mysqli_real_escape_string($con, $_REQUEST['txtndiscs'.$z]);
		
		$cacctno = "";

		$refcidenttran = $cPVNo."P".$z;
	
		if(!mysqli_query($con,"INSERT INTO `apv_d`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefno`, `crefinv`, `namount`, `cvatcode`, `nvatrate`, `nnet`, `nvatamt`, `cewtcode`, `newtrate`, `newtamt`, `napcm`, `napdisc`, `ndue`, `npayments`, `napplied`) values('$company', '$refcidenttran', '$z', '$cPVNo', '$crrno', '$ccustsi', $amnt, '$vtcode', '$vtrate', $vtnets, $vtvals, '$ewtcde', '$ewtrte', $ewtamt, $apcms, $apdiscs, $dueamt, $paymnt, $applid)")){
			
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
		$ndebit = mysqli_real_escape_string($con,$_REQUEST['txtdebit'.$z]);
		$ncredit = mysqli_real_escape_string($con,$_REQUEST['txtcredit'.$z]);
		//$nsubid = mysqli_real_escape_string($con,$_REQUEST['txtsubsid'.$z]);
		$cacctrem= mysqli_real_escape_string($con,$_REQUEST['txtacctrem'.$z]);
		$cacctpaytyp= mysqli_real_escape_string($con,$_REQUEST['selacctpaytyp'.$z]);

		$refcidenttran = $cPVNo."P".$z;
		
		mysqli_query($con,"INSERT INTO `apv_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `crefrr`, `cacctno`, `ctitle`, `cremarks`, `ndebit`, `ncredit`, `cacctrem`) values('$company', '$refcidenttran', '$z', '$cPVNo', '$crefrr', '$cacctno', '$ctitle', '$cacctrem', $ndebit, $ncredit,'$cacctpaytyp')");

	}

	if (!mysqli_query($con, "DELETE FROM `apv_deds` Where `compcode` = '$company' and `ctranno` = '$cPVNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}
	$rowcnt2 = $_REQUEST['hdnrowcnt2'];
	$zdc = 0;	 
	for($z=1; $z<=$rowcnt2; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcmrr'.$z]);
		$crefapcm = mysqli_real_escape_string($con,$_REQUEST['txtapcmdm'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtaccapcm'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdec'.$z]);
		$namt = mysqli_real_escape_string($con,$_REQUEST['txtapamt'.$z]);
		$apcmdte = mysqli_real_escape_string($con,$_REQUEST['txtapdte'.$z]);
		$cremrks = mysqli_real_escape_string($con,$_REQUEST['txtremz'.$z]);
		$wref = mysqli_real_escape_string($con,$_REQUEST['txtcmithref'.$z]);
		
		
		$refcidenttran = $cPVNo."P".$z;
		
		mysqli_query($con,"INSERT INTO `apv_deds`(`compcode`, `ctranno`, `cidentity`, `nidentity`, `cwithref`, `crefrr`, `crefapcm`, `drefapcmdate`, `ctype`, `namount`, `cremarks`, `cacctno`) values('$company', '$cPVNo', '$refcidenttran', '$z', $wref, '$crefrr', '$crefapcm', STR_TO_DATE('$apcmdte', '%m/%d/%Y'), 'CM', $namt, '$cremrks', '$cacctno')");
		
		$zdc = $z;

	}

	$rowcnt3 = $_REQUEST['hdnrowcnt3'];	 
	for($z=1; $z<=$rowcnt3; $z++){
		
		$crefrr = mysqli_real_escape_string($con,$_REQUEST['txtcmdcrr'.$z]);
		$cacctno = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdc'.$z]);
		$ctitle = mysqli_real_escape_string($con,$_REQUEST['txtaccapcmdecdc'.$z]);
		$namt = mysqli_real_escape_string($con,$_REQUEST['txtapdcamt'.$z]);
		$cremrks = mysqli_real_escape_string($con,$_REQUEST['txtremzdc'.$z]);
		
		$zdc++;
		$refcidenttran = $cPVNo."P".$zdc;
		
		mysqli_query($con,"INSERT INTO `apv_deds`(`compcode`, `ctranno`, `cidentity`, `nidentity`, `crefrr`, `ctype`, `namount`, `cremarks`, `cacctno`) values('$company', '$cPVNo', '$refcidenttran', '$zdc', '$crefrr', 'DISC', $namt, '$cremrks', '$cacctno')");

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
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>