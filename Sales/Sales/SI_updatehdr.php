<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


$company = $_SESSION['companyid'];


	$cSINo = $_REQUEST['txtcsalesno'];
	$cCustID = $_REQUEST['txtcustid'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = chkgrp($_REQUEST['txtremarks']); 
	$nGross = str_replace(",","",$_REQUEST['txtnGross']);
	//$selreinv = $_REQUEST['selreinv'];
	$selsitypz = $_REQUEST['selsityp'];	
	$selsiseries = chkgrp($_REQUEST['csiprintno']);
	$nnetvat = str_replace(",","",$_REQUEST['txtnNetVAT']);
	$nvat = str_replace(",","",$_REQUEST['txtnVAT']);

	$CurrCode = $_REQUEST['selbasecurr']; 
	$CurrDesc = $_REQUEST['hidcurrvaldesc'];  
	$CurrRate= $_REQUEST['basecurrval']; 
	$BaseGross= str_replace(",","",$_REQUEST['txtnBaseGross']);

	$RefMods= $_REQUEST['txtrefmod']; 
	$RefModsNo= $_REQUEST['txtrefmodnos']; 
	
	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";
	$cvatcode = "NULL";

				$sqlhead = mysqli_query($con,"Select cacctcodesales, cvattype from customers where compcode='$company' and cempid='$cCustID'");
				if (mysqli_num_rows($sqlhead)!=0) {
					$row = mysqli_fetch_assoc($sqlhead);
					$cacctcode = "'".$row["cacctcodesales"]."'";
					$cvatcode = "'".$row["cvattype"]."'";
				}


	if (!mysqli_query($con, "UPDATE sales set `ccode` = '$cCustID', `cremarks` = $cRemarks, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `nnet` = '$nnetvat', `nvat` = '$nvat', `cacctcode` = $cacctcode, `cvatcode` = $cvatcode, `lapproved` = 0, `csalestype` = '$selsitypz', `csiprintno` = $selsiseries, `nbasegross` = '$BaseGross', `ccurrencycode` = '$CurrCode', `ccurrencydesc` = '$CurrDesc', `nexchangerate` = '$CurrRate', `crefmodule` = '$RefMods', `crefmoduletran` = '$RefModsNo' where `compcode` = '$company' and `ctranno` = '$cSINo'")) {
		echo "False";

		
	} 
	else {
		echo $cSINo;
	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','SALES INVOICE','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from sales_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from sales_t_info Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from sales_t_disc Where compcode='$company' and ctranno='$cSINo'");

?>