<?php
session_start();
include('../../Connection/connection_string.php');
include('../../include/denied.php');

	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";

	$company = $_SESSION['companyid'];

	$cSINo = mysqli_real_escape_string($con, $_POST['txtctranno']);
	$cAcctNo =  mysqli_real_escape_string($con, $_POST['txtcacctid']);
	$cCustID =  mysqli_real_escape_string($con, $_POST['txtcustid']);
	$dTranDate = $_POST['date_delivery'];
	$cRemarks =  mysqli_real_escape_string($con, $_POST['txtremarks']); 
	//$receipt = mysqli_real_escape_string($con, $_POST['receipt']);
	//$cPayType =  mysqli_real_escape_string($con, $_POST['selpaytype']);
	$cPayType = "";
	$cPayMethod =  mysqli_real_escape_string($con, $_POST['selpayment']);
	$cORNo =  mysqli_real_escape_string($con, $_POST['txtORNo']); 
	$nGross =  mysqli_real_escape_string($con, $_POST['txtnGross']);
	$nGross = str_replace(",","",$nGross);

	$nApplied =  mysqli_real_escape_string($con, $_POST['txtnApplied']);
	$nApplied = str_replace(",","",$nApplied);

	$cOTDesc = "";
	$cOTRef = "";
	if ($cPayMethod!=="cash" && $cPayMethod!=="cheque"){
		$cOTDesc = mysqli_real_escape_string($con, $_POST['txtOTBankName']);
		$cOTRef = mysqli_real_escape_string($con, $_POST['txtOTRefNo']);	
	}

	$dret = 0;
	if(isset($_POST['isNoRef'])){
		$dret = $_POST['isNoRef'];
	}
	
	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate = $_REQUEST['basecurrval'];

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);

	//, `receipt_code` = '$receipt'
	if (!mysqli_query($con, "UPDATE `receipt` set `ccode` = '$cCustID', `dcutdate` = STR_TO_DATE('$dTranDate', '%m/%d/%Y'), `cpaymethod` = '$cPayMethod', `cpaytype` = '$cPayType', `cremarks` = '$cRemarks', `namount` = $nGross, `napplied` = $nApplied, `cacctcode` = '$cAcctNo', `cpaydesc` = '$cOTDesc', `cpayrefno` = '$cOTRef', `lnosiref` = $dret, `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate' where `compcode`='$company' and `ctranno`= '$cSINo'")) {
				
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


	

//DELETE CASH AND CHEQUE TABLE...
if (!mysqli_query($con, "DELETE FROM `receipt_cash_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
					printf("Errormessage: %s\n", mysqli_error($con));
}

if (!mysqli_query($con, "DELETE FROM `receipt_check_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
					printf("Errormessage: %s\n", mysqli_error($con));
}

if (!mysqli_query($con, "DELETE FROM `receipt_others_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
	printf("Errormessage: %s\n", mysqli_error($con));
}


if ($cPayMethod=="cash") { //INSERT CASH DETAILS
	$cvar1000 = mysqli_real_escape_string($con, $_POST['txtDenom1000']);
	if(is_numeric($cvar1000)){
				$namt = 1000*$cvar1000;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '1000', $cvar1000, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar500 = mysqli_real_escape_string($con, $_POST['txtDenom500']);
	if(is_numeric($cvar500)){
				$namt = 500*$cvar500;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '500', $cvar500, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar200 = mysqli_real_escape_string($con, $_POST['txtDenom200']);
	if(is_numeric($cvar200)){
				$namt = 200*$cvar200;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '200', $cvar200, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar100 = mysqli_real_escape_string($con, $_POST['txtDenom100']);
	if(is_numeric($cvar100)){
				$namt = 100*$cvar100;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '100', $cvar100, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar50 = mysqli_real_escape_string($con, $_POST['txtDenom50']);
	if(is_numeric($cvar50)){
				$namt = 50*$cvar50;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '50', $cvar50, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar20 = mysqli_real_escape_string($con, $_POST['txtDenom20']);
	if(is_numeric($cvar20)){
				$namt = 20*$cvar20;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '20', $cvar20, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar10 = mysqli_real_escape_string($con, $_POST['txtDenom10']);
	if(is_numeric($cvar10)){
				$namt = 10*$cvar10;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '10', $cvar10, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar5 = mysqli_real_escape_string($con, $_POST['txtDenom5']);
	if(is_numeric($cvar5)){
				$namt = 5*$cvar5;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '5', $cvar5, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar1 = mysqli_real_escape_string($con, $_POST['txtDenom1']);
	if(is_numeric($cvar1)){
				$namt = 5*$cvar1;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '1', $cvar1, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar025 = mysqli_real_escape_string($con, $_POST['txtDenom025']);
	if(is_numeric($cvar025)){
				$namt = 0.25*$cvar025;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '0.25', $cvar025, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar010 = mysqli_real_escape_string($con, $_POST['txtDenom010']);
	if(is_numeric($cvar010)){
				$namt = 0.10*$cvar010;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '0.10', $cvar010, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
	
	$cvar005 = mysqli_real_escape_string($con, $_POST['txtDenom005']);
	if(is_numeric($cvar005)){
				$namt = 0.05*$cvar005;
				if (!mysqli_query($con, "INSERT INTO `receipt_cash_t`(`compcode`, `ctranno`, `ndenomination`, `npieces`, `namount`) values('$company', '$cSINo', '0.05', $cvar005, $namt)")) {
					printf("Errormessage: %s\n", mysqli_error($con));
				} 
	
	}
}
elseif ($cPayMethod=="cheque"){ //INSERT CHEQUE DETAILS
	$CHKbank = mysqli_real_escape_string($con, $_POST['txtBankName']);
	$CHKdate = mysqli_real_escape_string($con, $_POST['txtChekDate']);
	$CHKchkno = mysqli_real_escape_string($con, $_POST['txtCheckNo']); 
	$CHKchkamt = mysqli_real_escape_string($con, $_POST['txtCheckAmt']);
	$CHKchkamt = str_replace(",","",$CHKchkamt);
	
				if (!mysqli_query($con, "INSERT INTO `receipt_check_t`(`compcode`, `ctranno`, `cbank`, `ccheckno`, `ddate`, nchkamt) values('$company', '$cSINo', '$CHKbank', '$CHKchkno', STR_TO_DATE('$CHKdate', '%m/%d/%Y'), $CHKchkamt)")) {
					
					printf("INSERT INTO `receipt_check_t`(`compcode`, `ctranno`, `cbank`, `ccheckno`, `ddate`, nchkamt) values('$company', '$cSINo', '$CHKbank', '$CHKchkno', STR_TO_DATE('$CHKdate', '%m/%d/%Y'), $CHKchkamt)\n", mysqli_error($con));
					
					printf("Errormessage: %s\n", mysqli_error($con));
				} 

}


//INSERT SALES DETAILS if Sales and Sales Type
$rowcntS = $_POST['hdnrowcnt'];
if($rowcntS!=0){	

	if (!mysqli_query($con, "DELETE FROM `receipt_sales_t` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$cnt = 0;	 
	for($z=1; $z<=$rowcntS; $z++){
		
		$csalesno = $_POST['txtcSalesNo'.$z];
				
		$namount = str_replace(",","",$_POST['txtSIGross'.$z]);
		$ndm = str_replace(",","",$_POST['txtndebit'.$z]);
		$ncm = str_replace(",","",$_POST['txtncredit'.$z]);
		$npayments = str_replace(",","",$_POST['txtnpayments'.$z]);

		/*$cvatcode = str_replace(",","",$_POST['txtnvatcode'.$z]);
		$nvatrate = str_replace(",","",$_POST['txtnvatrate'.$z]);
		$nvat = str_replace(",","",$_POST['txtvatamt'.$z]);
		$nnetamt = str_replace(",","",$_POST['txtnetvat'.$z]);
		$cvatcode1 = str_replace(",","",$_POST['txtnvatcodeorig'.$z]);

		if(isset($_POST['txtnEWT'.$z])){
			$ewtcode = implode(",",$_POST['txtnEWT'.$z]);
		}else{
			$ewtcode = "";
		}

		$ewtrate = str_replace(",","",$_POST['txtnEWTRate'.$z]);

		if($ewtrate==""){
			$ewtrate = 0;
		}else{
			$ewtrate = str_replace(";",",",$ewtrate);
		}

		$ewtamt = str_replace(",","", $_POST['txtnEWTAmt'.$z]);
		$ewtcode1 = $_POST['txtnEWTorig'.$z];*/
				
		$ndue = str_replace(",","",$_POST['txtDue'.$z]);
		$napplied = str_replace(",","",$_POST['txtApplied'.$z]);
		
		$cacctno = $_POST['txtcSalesAcctNo'.$z];
					
		$cnt = $cnt + 1;

		$refcidenttran = $cSINo."P".$cnt;

		//, `ctaxcode`, `ctaxcodeorig`, `nnet`, `nvat`, `ntaxrate`, `cewtcode`, `cewtcodeorig`, `newtrate`, `newtamt`
//, '$cvatcode', '$cvatcode1', $nnetamt, $nvat, $nvatrate, '$ewtcode', '$ewtcode1', '$ewtrate', $ewtamt

			if (!mysqli_query($con, "INSERT INTO `receipt_sales_t`(`compcode`, `cidentity`, `nidentity`, `ctranno`, `csalesno`, `namount`, `ndue`, `ndm`, `ncm`, `npayment`, `napplied`, `cacctno`) values('$company', '$refcidenttran', '$cnt', '$cSINo', '$csalesno', $namount, $ndue, $ndm, $ncm, $npayments, $napplied, '$cacctno')")) {
				
				printf("Errormessage: %s\n", mysqli_error($con));
			} 

	}
	
}

//INSERT CM/DM REFERENCES
$rowcntcmdm = $_POST['hdnrowcntcmdm'];
if($rowcntcmdm!=0){

	if (!mysqli_query($con, "DELETE FROM `receipt_deds` where `compcode`='$company' and `ctranno`= '$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	}

	$cnt = 0;	 
	for($z=1; $z<=$rowcntS; $z++){

		$cnt++;

		$adjtype = $_POST['hdnctypeadj'.$z];
		$adjrefsi = $_POST['hdndetsino'.$z];
		$adjtrano = $_POST['txtapcmdm'.$z];
		$adjdte = $_POST['txtapdte'.$z];
		$adjgrss = str_replace(",","",$_POST['txtapamt'.$z]);
		$adjremz = $_POST['txtremz'.$z]; 
		$adjisgiven = $_POST['hdnisgiven'.$z];

		$refcidenttran = $cSINo."P".$cnt;

		if (!mysqli_query($con, "INSERT INTO `receipt_deds`(`compcode`,`cidentity`,`nidentity`,`ctranno`,`aradjustment_ctype`,`aradjustment_ctranno`,`aradjustment_crefsi`,`aradjustment_dcutdate`,`aradjustment_ngross`,`cremarks`,`isgiven`) values('$company', '$refcidenttran', '$cnt', '$cSINo','$adjtype','$adjtrano','$adjrefsi', '$adjdte', $adjgrss, '$adjremz', '$adjisgiven')")){
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}

}

//INSERT OTHERS
$rowcntothers = $_POST['hdnOthcnt'];
if($rowcntothers!=0){
	$cnt = 0;	 
	for($z=1; $z<=$rowcntothers; $z++){

		$cnt++;

		$othracctID = $_POST['txtacctitleID'.$z];
		$othracctTITLE = $_POST['txtacctitle'.$z];
		$othrdbt = str_replace(",","",$_POST['txtnotDR'.$z]);
		$othrcrd = str_replace(",","",$_POST['txtnotCR'.$z]);

		$refcidenttran = $cSINo."P".$cnt;

		if (!mysqli_query($con, "INSERT INTO `receipt_others_t`(`compcode`,`cidentity`,`nidentity`,`ctranno`,`cacctno`,`ctitle`,`ncredit`,`ndebit`) values('$company', '$refcidenttran', '$cnt', '$cSINo','$othracctID','$othracctTITLE','$othrcrd', '$othrdbt')")){
			printf("Errormessage: %s\n", mysqli_error($con));
		}

	}

}


//insert attachment
$files = array_filter($_FILES['upload']['name']); //Use something similar before processing files.
// Count the number of uploaded files in array
$total_count = count($_FILES['upload']['name']);

if(file_exists('../../Components/assets/OR/'.$company.'_'.$cSINo.'/')) {
	/*$allfiles = scandir('../../RFP_Files/'.$cSINo.'/');
	$files = array_diff($allfiles, array('.', '..'));
	foreach($files as $file) {
		unlink("../../RFP_Files/".$cSINo."/".$file);
	}*/
}else{
	if($total_count>=1){
		mkdir('../../Components/assets/OR/'.$company.'_'.$cSINo.'/',0777);
	}
}

// Loop through every file
for( $i=0 ; $i < $total_count ; $i++ ) {
	//The temp file path is obtained
	$tmpFilePath = $_FILES['upload']['tmp_name'][$i];
	//A file path needs to be present
	if ($tmpFilePath != ""){
			//Setup our new file path
			$newFilePath = "../../Components/assets/OR/" .$company.'_'. $cSINo . "/" . $_FILES['upload']['name'][$i];
			//File is uploaded to temp dir
			move_uploaded_file($tmpFilePath, $newFilePath);
			
	}
}


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','RECEIVE PAYMENT','$compname','Updated Record')");


//$xurl = "";
	//if($dret==0){
		$xurl = "th_acctentry.php";
	//}else{
	//	$xurl = "OR_edit2.php";
	//}
?>
<form action="<?=$xurl?>" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
  document.forms['frmpos'].submit();
</script>