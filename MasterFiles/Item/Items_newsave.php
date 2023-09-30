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

	$cItemNo = strtoupper($_REQUEST['txtcpartno']);
	$cSKUCode =  mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcSKU']));
	$cItemDesc =  mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	$cNotes = chkgrp(mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcnotes'])));
	
	$cUnit = $_REQUEST['seluom'];
	$cClass = $_REQUEST['selclass'];
	$cType = $_REQUEST['seltype']; 
	$Seltax = $_REQUEST['seltax']; 
	$cTradeType = $_REQUEST['seltradetype'];
	 
	$PriceTyp = $_REQUEST['selitmpricing'];
	$nMarkUp = $_REQUEST['txtcmarkUp']; 
	
	$SalesCode = $_REQUEST['txtsalesacctD'];
	$WRRCode = $_REQUEST['txtrracctD'];
	$COGCode = $_REQUEST['txtcogacctD'];
	$DRCode = $_REQUEST['txtdracctD'];
	$SRetCode = $_REQUEST['txtretacctD'];
	
	$cGrp1 = chkgrp($_REQUEST['txtcGroup1D']);
	$cGrp2 = chkgrp($_REQUEST['txtcGroup2D']);
	$cGrp3 = chkgrp($_REQUEST['txtcGroup3D']);
	$cGrp4 = chkgrp($_REQUEST['txtcGroup4D']);
	$cGrp5 = chkgrp($_REQUEST['txtcGroup5D']);
	$cGrp6 = chkgrp($_REQUEST['txtcGroup6D']);
	$cGrp7 = chkgrp($_REQUEST['txtcGroup7D']);
	$cGrp8 = chkgrp($_REQUEST['txtcGroup8D']);
	$cGrp9 = chkgrp($_REQUEST['txtcGroup9D']);
	$cGrp10 = chkgrp($_REQUEST['txtcGroup10D']);

	$SelSITyp = $_REQUEST['selsityp']; 
	//$InvMin = $_REQUEST['txtInvMin'];
	//$InvMax = $_REQUEST['txtInvMax'];
	//$InvRoPt = $_REQUEST['txtInvRO'];
	
	$preparedby = $_SESSION['employeeid'];
	
	if(isset($_REQUEST['chkSer'])){
		$chkSer = 1;
	}
	else{
		$chkSer = 0;
	}

	if(isset($_REQUEST['chkbarcoded'])){
		$chkBCode = 1;
	}
	else{
		$chkBCode = 0;
	}

	if(isset($_REQUEST['chkInventoriable'])){
		$chkInvChk= 1;
	}
	else{
		$chkInvChk = 0;
	}

	//if(isset($_REQUEST['chkPack'])){
		//$chkPckd = 1;
	//}
	//else{
		//$chkPckd = 0;
	//}

	
	//INSERT NEW ITEM , `lpack`, `ninvmin` , `ninvmax` , `ninvordpt`
	// ,$chkInvChk ,$InvMin,$InvMax,$InvRoPt

	if (!mysqli_query($con, "INSERT INTO `items`(`compcode`, `cpartno`, `cskucode`, `citemdesc`, `cunit`, `cclass`, `ctype`, `ctaxcode`, `cpricetype`, `nmarkup`, `cacctcodesales`, `cacctcodewrr`, `cacctcodedr`, `cacctcoderet`, `cacctcodecog`, `cGroup1`, `cGroup2`, `cGroup3`, `cGroup4`, `cGroup5`, `cGroup6`, `cGroup7`, `cGroup8`, `cGroup9`, `cGroup10`, `cnotes`, `lSerial`, `lbarcode`, `linventoriable`, `csalestype`, `ctradetype`) VALUES ('$company', '$cItemNo', '$cSKUCode', '$cItemDesc', '$cUnit', '$cClass', '$cType', '$Seltax', '$PriceTyp', '$nMarkUp', '$SalesCode', '$WRRCode', '$DRCode', '$SRetCode', '$COGCode', $cGrp1, $cGrp2, $cGrp3, $cGrp4, $cGrp5, $cGrp6, $cGrp7, $cGrp8, $cGrp9, $cGrp10, $cNotes,$chkSer,$chkBCode,$chkInvChk,'$SelSITyp','$cTradeType')"));	
	{
		if(mysqli_error($con)!=""){
			echo "Error Main: ".mysqli_error($con);
		}
	}

	$MMRRowCnt = $_REQUEST['hdnseclvlrowcnt'];
	//INSERT MinMaxPerWhse
	if($MMRRowCnt>=1){
		//echo $UnitRowCnt;
		for($z=1; $z<=$MMRRowCnt; $z++){
			$cWhse = $_REQUEST['selitmsec'.$z];
			$cMin = $_REQUEST['txtwhmin'.$z];
			$cMax = $_REQUEST['txtwhmax'.$z]; 
			$cReOrPt = $_REQUEST['txtwhreor'.$z];

			if (!mysqli_query($con, "INSERT INTO `items_invlvl`(`compcode`, `cpartno`, `section_nid`, `nmin`, `nmax`, `nreorderpt`) VALUES ('$company','$cItemNo','$cWhse','$cMin','$cMax','$cReOrPt')")) {
				if(mysqli_error($con)!=""){
					echo "Error UOM: ".mysqli_error($con);
				}
			} 

		}
	}

	$UnitRowCnt = $_REQUEST['hdnunitrowcnt'];
	//INSERT FACTOR IF MERON
	if($UnitRowCnt>=1){
		//echo $UnitRowCnt;
		for($z=1; $z<=$UnitRowCnt; $z++){
			$cItemUnit = $_REQUEST['selunit'.$z];
			$cItemRule = $_REQUEST['selrule'.$z];
			$cItemFactor = $_REQUEST['txtfactor'.$z];
			if(isset($_REQUEST['txtchkPO'.$z])){
				$cPO = 1;
			}else{
				$cPO = 0;
			}

			if(isset($_REQUEST['txtchkSI'.$z])){
				$cSI = 1;
			}else{
				$cSI = 0;
			}
					
			//mysqli_query($con,"INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `npurchcost`, `nretailcost`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cItemPurch,$cItemRetail)");
			
			if (!mysqli_query($con, "INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `lpounit`, `lsiunit`, `crule`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cPO,$cSI,'$cItemRule')")) {
				if(mysqli_error($con)!=""){
					echo "Error UOM: ".mysqli_error($con);
				}
			} 

			$cItemUnit = "";
			$cItemFactor = 0;

		}
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'INSERTED','ITEM','$compname','Insert New Item')");



	echo "True";
?>
