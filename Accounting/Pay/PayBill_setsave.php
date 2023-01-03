<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$preparedby = mysqli_real_escape_string($con, $_SESSION['employeeid']);
	$company = $_SESSION['companyid'];	

function INSERTVAL($cid,$cval){
	global $con;
	global $company;
	
	if (!mysqli_query($con, "INSERT INTO `parameters`(`compcode`, `ccode`, `cvalue`, `norder`) values('$company', '$cid', '$cval', 1)")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
}

function UPDATEVAL($cid,$cval){
	global $con;
	global $company;
	
	if (!mysqli_query($con, "UPDATE `parameters` set `cvalue` = '$cval' Where `ccode`='$cid'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
				
	} 
}

	$nDebit =  mysqli_real_escape_string($con, $_REQUEST['paycreditchkid']); //CVCRCHK
	$nCredit = mysqli_real_escape_string($con, $_REQUEST['paycreditid']); //CVCRCASH
	$cPrepared =  mysqli_real_escape_string($con, $_REQUEST['cprepared']); //CVPREP
	$cReviewed =  mysqli_real_escape_string($con, $_REQUEST['creviewed']); //CVREVW
	$cVerified =  mysqli_real_escape_string($con, $_REQUEST['cverified']); //CVVERI
	$cApproved =  mysqli_real_escape_string($con, $_REQUEST['capproved']); //CVAPPR
	
//ChekACCOUNT
	$sqlchk = mysqli_query($con,"Select * From parameters where ccode='CVCRCHK'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nDebit){ UPDATEVAL('CVCRCHK',$nDebit); }

	}
	else{ 
		if($nDebit<>"") { INSERTVAL('CVCRCHK',$nDebit); }
	}

//CashACCOUNT
	$sqlchk = mysqli_query($con,"Select * From parameters where ccode='CVCRCASH'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nCreditDef = $row['cvalue'];
		}
		
			if ($nCreditDef<>$nCredit){ UPDATEVAL('CVCRCASH',$nCredit); }

	}
	else{ 
		if($nCredit<>"") { INSERTVAL('CVCRCASH',$nCredit); }
	}
	
//PREPARED BY
	$sqlchk = mysqli_query($con,"Select * From parameters where ccode='CVPREP'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nPreparedDef = $row['cvalue'];
		}
		
			if ($nPreparedDef<>$cPrepared){ UPDATEVAL('CVPREP',$cPrepared); }

	}
	else{ 
		if($cPrepared<>"") { INSERTVAL('CVPREP',$cPrepared); }
	}


//REVIEWED BY
	$sqlchk = mysqli_query($con,"Select * From parameters where ccode='CVREVW'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nReviewDef = $row['cvalue'];
		}
		
			if ($nReviewDef<>$cReviewed){ UPDATEVAL('CVREVW',$cReviewed); }

	}
	else{ 
		if($cReviewed<>"") { INSERTVAL('CVREVW',$cReviewed); } 
	}

//VERIFIED BY
	$sqlchk = mysqli_query($con,"Select * From parameters where ccode='CVVERI'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nVeriDef = $row['cvalue'];
		}
		
			if ($nVeriDef<>$cVerified){ UPDATEVAL('CVVERI',$cVerified); }

	}
	else{ 
		if($cVerified<>"") { INSERTVAL('CVVERI',$cVerified); } 
	}

//APPROVED BY
	$sqlchk = mysqli_query($con,"Select * From parameters where ccode='CVAPPR'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nAppDef = $row['cvalue'];
		}
		
			if ($nAppDef<>$cApproved){ UPDATEVAL('CVAPPR',$cApproved); }

	}
	else{ 
		if($cApproved<>"") { INSERTVAL('CVAPPR',$cApproved); }
	}


//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','CV','$preparedby',NOW(),'UPDATED','CV SETTINGS','$compname','Updated Record')");


	echo "CV Setup Succesfully Saved!";
?>
