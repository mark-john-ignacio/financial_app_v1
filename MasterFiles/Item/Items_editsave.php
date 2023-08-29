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
$mymsg = "True";
$myerror = "True";

	$cItemNo = strtoupper($_REQUEST['txtcpartno']);
	$SKUCode =  mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcSKU']));
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
	$InvMin = $_REQUEST['txtInvMin'];
	$InvMax = $_REQUEST['txtInvMax'];
	$InvRoPt = $_REQUEST['txtInvRO'];
	
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

	if(isset($_REQUEST['chkPack'])){
		$chkPckd = 1;
	}
	else{
		$chkPckd = 0;
	}
	//INSERT NEW ITEM , `cpricetype`='$PriceTyp'
	if (!mysqli_query($con, "UPDATE `items` set `cskucode` = '$SKUCode', `citemdesc` = '$cItemDesc', `cunit` = '$cUnit', `cclass` = '$cClass', `ctype` = '$cType', `ctaxcode` = '$Seltax', `cpricetype`='$PriceTyp', `nmarkup`='$nMarkUp', `cacctcodesales` = '$SalesCode', `cacctcodewrr` = '$WRRCode', `cacctcodedr` = '$DRCode', `cacctcoderet` = '$SRetCode', `cacctcodecog` = '$COGCode', `cGroup1` = $cGrp1, `cGroup2` = $cGrp2, `cGroup3` = $cGrp3, `cGroup4` = $cGrp4, `cGroup5` = $cGrp5, `cGroup6` = $cGrp6, `cGroup7` = $cGrp7, `cGroup8` = $cGrp8, `cGroup9` = $cGrp9, `cGroup10` = $cGrp10, `cnotes` = $cNotes, `lSerial` = $chkSer, `lbarcode` = $chkBCode, `lpack` = $chkPckd, `ninvmin` = $InvMin , `ninvmax` = $InvMax , `ninvordpt` = $InvRoPt , `csalestype` = '$SelSITyp', `ctradetype` = '$cTradeType' where `compcode` = '$company' and `cpartno` = '$cItemNo'"));	
	{
		if(mysqli_error($con)!=""){
			$myerror =  "Error Main: ".mysqli_error($con);
		}
	}

			if (!mysqli_query($con, "DELETE from `items_factor` where `compcode` = '$company' and `cpartno` = '$cItemNo'")) {
					if(mysqli_error($con)!=""){
						$myerror =  "Error UOM DEL: ".mysqli_error($con);
					}
			} 

	$UnitRowCnt = $_REQUEST['hdnunitrowcnt'];
	//INSERT FACTOR IF MERON
	if($UnitRowCnt>=1){

		for($z=1; $z<=$UnitRowCnt; $z++){
			$cItemUnit = $_REQUEST['selunit'.$z];
			$cItemFactor = $_REQUEST['txtfactor'.$z];
						
			if (!mysqli_query($con, "INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit')")) {
					if(mysqli_error($con)!=""){
						$myerror =  "Error UOM: ".mysqli_error($con);
					}
			} 

			$cItemUnit = "";
			$cItemFactor = 0;

		}
	}


	if (!mysqli_query($con, "DELETE from `items_process_t` where `compcode` = '$company' and `citemno` = '$cItemNo'")) {
		if(mysqli_error($con)!=""){
			$myerror =  "Error PROCESSES DEL: ".mysqli_error($con);
		}
	} 

	$UnitRowCnt = $_REQUEST['hdnprocesslist'];
	//INSERT FACTOR IF MERON
	if($UnitRowCnt>=1){
		//echo $UnitRowCnt;
		for($z=1; $z<=$UnitRowCnt; $z++){
			$cItemProc = $_REQUEST['selproc'.$z];
			
			//mysqli_query($con,"INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `npurchcost`, `nretailcost`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cItemPurch,$cItemRetail)");
			
			if (!mysqli_query($con, "INSERT INTO `items_process_t`(`compcode`, `items_process_id`, `citemno`) VALUES ('$company','$cItemProc','$cItemNo')")) {
					if(mysqli_error($con)!=""){
						echo "Error UOM: ".mysqli_error($con);
					}
			} 
			$cItemProc = 0;

		}
	}


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'UPDATED','ITEM','$compname','Updated Item')");


//IMAGE UPLOADING

//For Uploading photo
if($_FILES["file"]["name"]!="")
{
$validextensions = array("jpeg", "jpg", "png");
$temporary = explode(".", $_FILES["file"]["name"]);
$file_extension = end($temporary);

	if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
	) && ($_FILES["file"]["size"] < 500000)//Approx. 100kb files can be uploaded.
	&& in_array($file_extension, $validextensions)) {
		if ($_FILES["file"]["error"] > 0)
		{
			$myerror = "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
		}
		else
		{
			if (file_exists("../../imgitm/" . $_FILES["file"]["name"])) {
				unlink ("../../imgitm/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../../imgitm/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtargetPath = "../../imgitm/".$cItemNo.".".$file_extension; // to rename the image to userid
				move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
				//echo "\nImage Uploaded Successfully...!!";
				//echo "\nFile Name: " . $newtargetPath;
				//echo "\nFile Type: " . $_FILES["file"]["type"];
				//echo "\nFile Size: " . ($_FILES["file"]["size"] / 1024) . " kB";

				//update file name in users table
				if (!mysqli_query($con, "UPDATE items set cuserpic = '$newtargetPath' where `compcode` = '$company' and `cpartno` = '$cItemNo'")) {
					if(mysqli_error($con)!=""){
						$myerror = "Update Error: ". mysqli_error($con)."<br/><br/>";
					}
				}

			
		}
	}
	else
	{
		$mymsg = "Size";
	}
}
else {
	$mymsg = "NO";
}

	if($myerror != "True"){
		echo $myerror;
	}else{
		echo $mymsg;
	}
?>
