<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


$company = $_SESSION['companyid'];

	$cItemNo = strtoupper($_REQUEST['txtcpartno']);
	$cItemDesc =  mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	$cNotes = chkgrp(mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcnotes'])));
	
	$cUnit = $_REQUEST['seluom'];
	$cClass = $_REQUEST['selclass'];
	$cType = $_REQUEST['seltype']; 
	$Seltax = $_REQUEST['seltax'];
	
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
	
	$preparedby = $_SESSION['employeeid'];
	
	//INSERT NEW ITEM
	if (!mysqli_query($con, "INSERT INTO `items`(`compcode`, `cpartno`, `citemdesc`, `cunit`, `cclass`, `ctype`, `ctaxcode`, `cpricetype`, `nmarkup`, `cacctcodesales`, `cacctcodewrr`, `cacctcodedr`, `cacctcoderet`, `cacctcodecog`, `cGroup1`, `cGroup2`, `cGroup3`, `cGroup4`, `cGroup5`, `cGroup6`, `cGroup7`, `cGroup8`, `cGroup9`, `cGroup10`, `cnotes`) VALUES ('$company', '$cItemNo', '$cItemDesc', '$cUnit', '$cClass', '$cType', '$Seltax', '$PriceTyp', '$nMarkUp', '$SalesCode', '$WRRCode', '$DRCode', '$SRetCode', '$COGCode', $cGrp1, $cGrp2, $cGrp3, $cGrp4, $cGrp5, $cGrp6, $cGrp7, $cGrp8, $cGrp9, $cGrp10, $cNotes)"));	
	{
		if(mysqli_error($con)!=""){
			echo "Error Main: ".mysqli_error($con);
		}
	}

	$UnitRowCnt = $_REQUEST['hdnunitrowcnt'];
	//INSERT FACTOR IF MERON
	if($UnitRowCnt>=1){
		//echo $UnitRowCnt;
		for($z=1; $z<=$UnitRowCnt; $z++){
			$cItemUnit = $_REQUEST['selunit'.$z];
			$cItemFactor = $_REQUEST['txtfactor'.$z];
			
			//mysqli_query($con,"INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `npurchcost`, `nretailcost`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cItemPurch,$cItemRetail)");
			
			if (!mysqli_query($con, "INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit')")) {
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
