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
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);
	$ContactNo = chkgrp($_REQUEST['txtcontact']);
	$emailadd = chkgrp($_REQUEST['txtcEmail']);


	//IUPDATE ITEM
	if (!mysqli_query($con,"UPDATE `salesman` set `cname`='$cCustName', `chouseno` = $HouseNo, `ccity` = $City, `cstate` = $State, `ccountry` = $Country, `czip` = $ZIP, `ctelno` = $ContactNo, `cemailadd` = $emailadd Where compcode='$company' and `ccode`='$cCustCode'")){

					if(mysqli_error($con)!=""){
						$myerror = "Update Error: ". mysqli_error($con)."<br/><br/>";
					}
	}

		
//INSERT LOGFILE
	$compname = php_uname('n');
	$preparedby = $_SESSION['employeeid'];
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'UPDATED','SALESMAN','$compname','Update Salesman Details')");


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
			if (file_exists("../../imgsman/" . $_FILES["file"]["name"])) {
				unlink ("../../imgsman/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../../imgsman/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtargetPath = "../../imgsman/".$cCustCode.".".$file_extension; // to rename the image to userid
				move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
				//echo "\nImage Uploaded Successfully...!!";
				//echo "\nFile Name: " . $newtargetPath;
				//echo "\nFile Type: " . $_FILES["file"]["type"];
				//echo "\nFile Size: " . ($_FILES["file"]["size"] / 1024) . " kB";

				//update file name in users table
				if (!mysqli_query($con, "UPDATE salesman set cuserpic = '$newtargetPath' where compcode='$company' and `ccode`='$cCustCode'")) {
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
