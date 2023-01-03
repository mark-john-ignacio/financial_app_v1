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
	
	if (!mysqli_query($con, "UPDATE `parameters` set `cvalue` = '$cval' Where compcode='$company' and `ccode`='$cid'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
				
	} 
}

	$nCredit =  mysqli_real_escape_string($con, $_POST['loancrid']); 
	$nDebit =  mysqli_real_escape_string($con, $_POST['loanapid']); 
	$nInteres =  mysqli_real_escape_string($con, $_POST['loanintid']);
	$nCapital =  mysqli_real_escape_string($con, $_POST['loancapid']);
	$nServiceFee =  mysqli_real_escape_string($con, $_POST['loansrvid']);

//Credit Account
	$sqlchk = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='LOANCRACCT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nCredit){ UPDATEVAL('LOANCRACCT',$nCredit); }

	}
	else{ 
		if($nCredit<>"") { INSERTVAL('LOANCRACCT',$nCredit); }
	}


//Loan Account
	$sqlchk = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='LOANAPACCT'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nDebit){ UPDATEVAL('LOANAPACCT',$nDebit); }

	}
	else{ 
		if($nDebit<>"") { INSERTVAL('LOANAPACCT',$nDebit); }
	}

//Interest Account
	$sqlchk = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='LOANINTRST'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nInteres){ UPDATEVAL('LOANINTRST',$nInteres); }

	}
	else{ 
		if($nInteres<>"") { INSERTVAL('LOANINTRST',$nInteres); }
	}

//Capital Account
	$sqlchk = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='LOANCAPTL'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nCapital){ UPDATEVAL('LOANCAPTL',$nCapital); }

	}
	else{ 
		if($nCapital<>"") { INSERTVAL('LOANCAPTL',$nCapital); }
	}

//Service Account
	$sqlchk = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='LOANSRVFEE'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nServiceFee){ UPDATEVAL('LOANSRVFEE',$nServiceFee); }

	}
	else{ 
		if($nServiceFee<>"") { INSERTVAL('LOANSRVFEE',$nServiceFee); }
	}

//LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','LOAN APP','$preparedby',NOW(),'UPDATES','LOAN SETTINGS','$compname','Updated Record')");


	echo "Loan Application Setup Succesfully Saved! ".$nDebit;
?>
