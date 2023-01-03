<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

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
	$SalesCode = $_REQUEST['txtsalesacctD'];
	$Type = $_REQUEST['seltyp'];
	$Class = $_REQUEST['selcls'];
	$Terms = $_REQUEST['selterms'];
	
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);

	$Contact = chkgrp($_REQUEST['txtcperson']);
	$Desig = chkgrp($_REQUEST['txtcdesig']);
	$Email = chkgrp($_REQUEST['txtcEmail']);
	$PhoneNo = chkgrp($_REQUEST['txtcphone']);
	$Mobile = chkgrp($_REQUEST['txtcmobile']);
	
	$preparedby = $_SESSION['employeeid'];
	
	//INSERT NEW ITEM
	if (!mysqli_query($con,"UPDATE `suppliers` set `cname`='$cCustName', `cacctcode` = '$SalesCode', `cterms` = '$Terms',`csuppliertype` = '$Type', `csupplierclass` = '$Class', `chouseno` = $HouseNo, `ccity` = $City, `cstate` = $State, `ccountry` = $Country, `czip` = $ZIP, `cphone` = $PhoneNo, `cmobile` = $Mobile, `ccontactname` = $Contact, `cemail` = $Email, `cdesignation` = $Desig Where compcode='$company' and `ccode`='$cCustCode'")){
					if(mysqli_error($con)!=""){
						$myerror = "Update Error: ". mysqli_error($con)."<br/><br/>";
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
			if (file_exists("../imgsupp/" . $_FILES["file"]["name"])) {
				unlink ("../imgsupp/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../imgsupp/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtargetPath = "../imgsupp/".$cCustCode.".".$file_extension; // to rename the image to userid
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
