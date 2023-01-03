<?php
session_start();
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

	$nDebit =  mysqli_real_escape_string($con, $_POST['id']); //ORDEBITCASH
	$nDebit2 =  mysqli_real_escape_string($con, $_POST['id2']); //ORDEBITCHK

	
//DEBITACCOUNT CASH
	$sqlchk = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='ORDEBCASH'");
	if (mysqli_num_rows($sqlchk)!=0) {
		while($row = mysqli_fetch_array($sqlchk, MYSQLI_ASSOC)){
			$nDebitDef = $row['cvalue'];
		}
		
			if ($nDebitDef<>$nDebit){ UPDATEVAL('ORDEBCASH',$nDebit); }

	}
	else{ 
		if($nDebit<>"") { INSERTVAL('ORDEBIT',$nDebit); }
	}

//DEBITACCOUNT CHEQUE
	$sqlchk2 = mysqli_query($con,"Select * From parameters where compcode='$company' and ccode='ORDEBCHK'");
	if (mysqli_num_rows($sqlchk2)!=0) {
		while($row2 = mysqli_fetch_array($sqlchk2, MYSQLI_ASSOC)){
			$nDebitChk = $row2['cvalue'];
		}
		
			if ($nDebitChk<>$nDebit2){ UPDATEVAL('ORDEBCHK',$nDebit2); }

	}
	else{ 
		if($nDebit2<>"") { INSERTVAL('ORDEBCHK',$nDebit2); }
	}

//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','OR','$preparedby',NOW(),'UPDATES','OR SETTINGS','$compname','Updated Record')");


	echo "OR Setup Succesfully Saved! ".$nDebit;
?>
