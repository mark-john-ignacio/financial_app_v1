<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');
require_once('../../Model/helper.php');

$company = $_SESSION['companyid'];

function chkgrp($valz) {
	if($valz==''){
		return "''";
	}else{
    return "'".str_replace("'","\'",$valz)."'";
	}
}

	$cSINo = $_REQUEST['id'];
	$cCustID = $_REQUEST['ccode'];
	$dDelDate = $_REQUEST['ddate'];
	$cRemarks = chkgrp($_REQUEST['crem']); 
	$cRemarksLow = chkgrp($_REQUEST['cremlow']); 
	$nGross = $_REQUEST['ngross'];
	$nDRPrintNo = chkgrp($_REQUEST['cdrprintno']);

	$salesman = $_REQUEST['salesman'];
	$delcodes = $_REQUEST['delcodes'];
	$delhousno = chkgrp($_REQUEST['delhousno']);
	$delcity = chkgrp($_REQUEST['delcity']);
	$delstate = chkgrp($_REQUEST['delstate']);
	$delcountry = chkgrp($_REQUEST['delcountry']);
	$delzip = $_REQUEST['delzip'];
	
	$selSign1 = chkgrp($_REQUEST['selSign1']);
	$selSign2 = chkgrp($_REQUEST['selSign2']);

	$cdrapcord = chkgrp($_REQUEST['cdrapcord']);
	$cdrapcdr = chkgrp($_REQUEST['cdrapcdr']);

	$preparedby = $_SESSION['employeeid'];
	$cacctcode = "NULL";

	$sqlhead = mysqli_query($con,"Select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'");
	if (mysqli_num_rows($sqlhead)!=0) {
		$row = mysqli_fetch_assoc($sqlhead);
		$cacctcode = "'".$row["cacctcodesales"]."'";
	}

	//INSERT HEADER

	if (!mysqli_query($con, "UPDATE dr set `ccode` = '$cCustID', `clowremarks` = $cRemarksLow, `cremarks` = $cRemarks, `dcutdate` = STR_TO_DATE('$dDelDate', '%m/%d/%Y'), `ngross` = '$nGross', `cacctcode` = $cacctcode, `cdrprintno` = $nDRPrintNo, `csalesman` = '$salesman', `cdelcode` = '$delcodes', `cdeladdno` = $delhousno, `cdeladdcity` = $delcity, `cdeladdstate` = $delstate, `cdeladdcountry` = $delcountry, `cdeladdzip` = '$delzip', `crefapcord` = $cdrapcord, `crefapcdr` = $cdrapcdr, `csign1` = $selSign1, `csign2` = $selSign2 where `compcode` = '$company' and `ctranno` = '$cSINo'")) {  
		echo "False";
	} 
	else {
		echo $cSINo;
	}

	$txtpullrqs = chkgrp($_REQUEST['txtpullrqs']);
	$txtpullrmrks = chkgrp($_REQUEST['txtpullrmrks']);
	$txtRevNo = chkgrp($_REQUEST['txtRevNo']);
	$txtSalesRep = chkgrp($_REQUEST['txtSalesRep']);
	$txtTruckNo = chkgrp($_REQUEST['txtTruckNo']);
	$txtDelSch = chkgrp($_REQUEST['txtDelSch']);
	$txtRevOthers = chkgrp($_REQUEST['txtRevOthers']);
	$DRfootCert = chkgrp($_REQUEST['DRfootCert']);
	$DRfootIssu = chkgrp($_REQUEST['DRfootIssu']);
	$DRfootChec = chkgrp($_REQUEST['DRfootChec']);
	$DRfootAppr = chkgrp($_REQUEST['DRfootAppr']);

	//INSERT APCDR DETAILS
	mysqli_query($con,"UPDATE dr_apc_t set `cpull_advice` = $txtpullrqs, `cpull_remarks` = $txtpullrmrks, `crevno` = $txtRevNo, `csalesrep` = $txtSalesRep, `ctruckno` = $txtTruckNo, `cdelsched` = $txtDelSch, `cothers` = $txtRevOthers, `ccertified` = $DRfootCert, `cissued` = $DRfootIssu, `cchecked` = $DRfootChec, `capproved` = $DRfootAppr where `compcode` = '$company' and `ctranno` = '$cSINo'");

	
	if(count($_FILES) != 0){
		$directory = "../../Components/assets/DR/";
		if(!is_dir($directory)){
			mkdir($directory, 0777);
		}
		$directory .= "{$company}_{$cSINo}/";
		var_dump($directory);
		upload_image($_FILES, $directory);
	}
	
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'UPDATED','DELIVERY RECEIPT','$compname','Updated Record')");
	
	// Delete previous details
	mysqli_query($con, "Delete from dr_t Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from dr_t_info Where compcode='$company' and ctranno='$cSINo'");
	mysqli_query($con, "Delete from dr_t_serials Where compcode='$company' and ctranno='$cSINo'");	

?>