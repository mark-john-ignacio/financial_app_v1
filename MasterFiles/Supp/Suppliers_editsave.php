<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	function chkgrp($valz) {
		global $con;
		
		if($valz==''){
			return "NULL";
		}else{
				return "'".mysqli_real_escape_string($con, $valz)."'";
		}
	}

	$cCustCode = strtoupper($_REQUEST['txtccode']);
	$company = $_SESSION['companyid'];
	$mymsg = "True";
	$myerror = "True";
	
	$cCustName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	$cTradeName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txttradename']));

	$SalesCode = $_REQUEST['txtsalesacctD'];
	$SalesCodeID = $_REQUEST['txtsalesacctDID'];
	$Type = $_REQUEST['seltyp'];
	$Class = $_REQUEST['selcls'];
	$Terms = $_REQUEST['selterms'];
	$PROCUREMENT = $_REQUEST['procurement'];
	//$VatType = $_REQUEST['selvattype']; 
	//$VatTypeRate = $_REQUEST['txttaxrate'];
	$VatType = "";   
	$VatTypeRate = 0;
	$VatEWTCode = chkgrp($_REQUEST['txtewtD']); 
	$Tin = $_REQUEST['txtTinNo']; 
		
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);

	//$Contact = chkgrp($_REQUEST['txtcperson']); `cphone` = $PhoneNo, `cmobile` = $Mobile, `ccontactname` = $Contact, `cemail` = $Email, `cdesignation` = $Desig
	//$Desig = chkgrp($_REQUEST['txtcdesig']);
	//$Email = chkgrp($_REQUEST['txtcEmail']);
	//$PhoneNo = chkgrp($_REQUEST['txtcphone']);
	//$Mobile = chkgrp($_REQUEST['txtcmobile']);

	$cGrp1 = chkgrp($_REQUEST['txtCustGroup1D']);
	$cGrp2 = chkgrp($_REQUEST['txtCustGroup2D']);
	$cGrp3 = chkgrp($_REQUEST['txtCustGroup3D']);
	$cGrp4 = chkgrp($_REQUEST['txtCustGroup4D']);
	$cGrp5 = chkgrp($_REQUEST['txtCustGroup5D']);
	$cGrp6 = chkgrp($_REQUEST['txtCustGroup6D']);
	$cGrp7 = chkgrp($_REQUEST['txtCustGroup7D']);
	$cGrp8 = chkgrp($_REQUEST['txtCustGroup8D']);
	$cGrp9 = chkgrp($_REQUEST['txtCustGroup9D']);
	$cGrp10 = chkgrp($_REQUEST['txtCustGroup10D']);

	$SelCurr = $_REQUEST['selcurrncy'];
	
	$preparedby = $_SESSION['employeeid'];
	
	if (!mysqli_query($con,"UPDATE `suppliers` set `cname`='$cCustName', `ctradename`='$cTradeName', `cacctcode` = '$SalesCodeID', `cterms` = '$Terms',`csuppliertype` = '$Type', `csupplierclass` = '$Class', `chouseno` = $HouseNo, `ccity` = $City, `cstate` = $State, `ccountry` = $Country, `czip` = $ZIP, `cGroup1` = $cGrp1, `cGroup2` = $cGrp2, `cGroup3` = $cGrp3, `cGroup4` = $cGrp4, `cGroup5` = $cGrp5, `cGroup6` = $cGrp6, `cGroup7` = $cGrp7, `cGroup8` = $cGrp8, `cGroup9` = $cGrp9, `cGroup10` = $cGrp10, `cvattype` = '$VatType', `ctin` = '$Tin', `nvatrate` = $VatTypeRate, `newtcode` = $VatEWTCode, `cdefaultcurrency` = '$SelCurr', `procurement` = '$PROCUREMENT' Where compcode='$company' and `ccode`='$cCustCode'")){
					if(mysqli_error($con)!=""){  
						$myerror = "Update Error: ". mysqli_error($con)."<br/><br/>";
					}
	}

	//echo "UPDATE `suppliers` set `cname`='$cCustName', `ctradename`='$cTradeName', `cacctcode` = '$SalesCodeID', `cterms` = '$Terms',`csuppliertype` = '$Type', `csupplierclass` = '$Class', `chouseno` = $HouseNo, `ccity` = $City, `cstate` = $State, `ccountry` = $Country, `czip` = $ZIP, `cGroup1` = $cGrp1, `cGroup2` = $cGrp2, `cGroup3` = $cGrp3, `cGroup4` = $cGrp4, `cGroup5` = $cGrp5, `cGroup6` = $cGrp6, `cGroup7` = $cGrp7, `cGroup8` = $cGrp8, `cGroup9` = $cGrp9, `cGroup10` = $cGrp10, `cvattype` = '$VatType', `ctin` = '$Tin', `nvatrate` = $VatTypeRate, `newtcode` = $VatEWTCode Where compcode='$company' and `ccode`='$cCustCode'";


	//cntacts
		$UnitRowCnt = $_REQUEST['hdncontlistcnt'];
		//INSERT CONTACTS IF MERON
		if($UnitRowCnt>=1){
			mysqli_query($con,"DELETE FROM `suppliers_contacts` where ccode = '$cCustCode'");
			//echo $UnitRowCnt;

			$arridxcv = array();
			$sql = "Select * From contacts_types where compcode='$company'";
			$result=mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$arridxcv[] = $row['cid'];
			}

			for($z=1; $z<=$UnitRowCnt; $z++){
				$cIConNme = $_REQUEST['txtConNme'.$z];
				$cIConDes = $_REQUEST['txtConDes'.$z];
				$cIConDept = $_REQUEST['txtConDept'.$z];
				//$cIConEmail = $_REQUEST['txtConEmail'.$z];
				//$cIConMobile = $_REQUEST['txtConMob'.$z];

				if (!mysqli_query($con, "INSERT INTO `suppliers_contacts`(`compcode`, `ccode`, `cname`, `cdesignation`, `cdept`) VALUES ('$company','$cCustCode','$cIConNme','$cIConDes','$cIConDept')")) {
					echo "Error Contacts: ".mysqli_error($con);
				}else{
					$xcid = mysqli_insert_id($con);

					mysqli_query($con,"DELETE FROM `suppliers_contacts_nos` where customers_contacts_cid = '$xcid'");

					foreach($arridxcv as $rmnb){
						$xcvlxcz = $_REQUEST['txtConAdd'.$rmnb.$z];
						mysqli_query($con, "INSERT INTO `suppliers_contacts_nos`(`compcode`, `customers_contacts_cid`, `contact_type`, `cnumber`) VALUES ('$company','$xcid','$rmnb','$xcvlxcz')");
					}
				}
	
			}
		}
		
		$DelAddrsCnt = $_REQUEST['hdnaddresscnt'];
		//INSERT ADDRESS IF MERON
		if($DelAddrsCnt>=1){
			mysqli_query($con,"DELETE FROM `suppliers_address` where ccode = '$cCustCode'");
			//echo $UnitRowCnt;
			for($z=1; $z<=$DelAddrsCnt; $z++){
				$cDelAddNo = $_REQUEST['txtdeladdno'.$z];
				$cDelAddCt = $_REQUEST['txtdeladdcity'.$z]; 
				$cDelAddSt = $_REQUEST['txtdeladdstt'.$z];
				$cDelAddCr = $_REQUEST['txtdeladdcntr'.$z];
				$cDelAddZp = $_REQUEST['txtdeladdzip'.$z];
										
				if (!mysqli_query($con, "INSERT INTO `suppliers_address`(`compcode`, `ccode`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`) VALUES ('$company','$cCustCode','$cDelAddNo','$cDelAddCt','$cDelAddSt','$cDelAddCr','$cDelAddZp')")) {
						if(mysqli_error($con)!=""){
							echo "Error Addresses: ".mysqli_error($con);
						}
				} 
	
			}
		}


	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'UPDATED','SUPPLIERS','$compname','Updated Supplier Details')");


	//Delete product details
	
	mysqli_query($con,"DELETE FROM `items_suppliers` Where compcode='$company' and `ccode`='$cCustCode'");

//IMAGE UPLOADING

//For Uploading photo
if($_FILES["file"]["name"]!="")
{
$validextensions = array("jpeg", "jpg", "png");
$temporary = explode(".", $_FILES["file"]["name"]);
$file_extension = end($temporary);

	if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
	) && ($_FILES["file"]["size"] < 100000)//Approx. 100kb files can be uploaded.
	&& in_array($file_extension, $validextensions)) {
		if ($_FILES["file"]["error"] > 0)
		{
			$myerror = "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
		}
		else
		{
			if (file_exists("../../imgsupp/" . $_FILES["file"]["name"])) {
				unlink ("../../imgsupp/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../../imgsupp/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtargetPath = "../../imgsupp/".$cCustCode.".".$file_extension; // to rename the image to userid
				move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
				//echo "\nImage Uploaded Successfully...!!";
				//echo "\nFile Name: " . $newtargetPath;
				//echo "\nFile Type: " . $_FILES["file"]["type"];
				//echo "\nFile Size: " . ($_FILES["file"]["size"] / 1024) . " kB";

				//update file name in users table
				if (!mysqli_query($con, "UPDATE suppliers set cuserpic = '$newtargetPath' where compcode='$company' and `ccode`='$cCustCode'")) {
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
