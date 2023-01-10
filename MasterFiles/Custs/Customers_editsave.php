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
	
	$SalesCodeType = $_REQUEST['selaccttyp'];
	$CustTyp = $_REQUEST['seltyp'];
	$CustCls = $_REQUEST['selcls'];
	$CreditLimit = $_REQUEST['txtclimit'];
	//$CrripplesLimit = $_REQUEST['txtcriplimit'];
	$PriceVer = $_REQUEST['selpricever'];
	$VatType = $_REQUEST['selvattype'];
	$Terms = $_REQUEST['selcterms']; 
	$Tin = $_REQUEST['txtTinNo'];

	$PrentCode = chkgrp($_REQUEST['txtcparentD']);
	$csman = chkgrp($_REQUEST['txtsmanD']);

	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);
	
	$preparedby = $_SESSION['employeeid'];

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


	if($SalesCodeType=="single") {
		$SalesCode = $_REQUEST['txtsalesacctDID'];
	}else{
		$SalesCode = "";
	}

	$SalesCodeCR = $_REQUEST['txtsalesacctDIDCR'];

	//IUPDATE ITEM
	if (!mysqli_query($con,"UPDATE `customers` set `cname`='$cCustName', `ctradename` = '$cTradeName', `cacctcodesales` = '$SalesCode', `cacctcodesalescr` = '$SalesCodeCR', `cacctcodetype` = '$SalesCodeType', `ccustomertype`='$CustTyp', `ccustomerclass`='$CustCls', `cpricever` = '$PriceVer', `cvattype`='$VatType', `cterms` = '$Terms', `nlimit` = $CreditLimit, `chouseno` = $HouseNo, `ccity` = $City, `cstate` = $State, `ccountry` = $Country, `czip` = $ZIP, `cparentcode` = $PrentCode, `csman` = $csman, `cGroup1` = $cGrp1, `cGroup2` = $cGrp2, `cGroup3` = $cGrp3, `cGroup4` = $cGrp4, `cGroup5` = $cGrp5, `cGroup6` = $cGrp6, `cGroup7` = $cGrp7, `cGroup8` = $cGrp8, `cGroup9` = $cGrp9, `cGroup10` = $cGrp10 Where compcode='$company' and `cempid`='$cCustCode'")){

					if(mysqli_error($con)!=""){
						$myerror = "Update Error: ". mysqli_error($con)."<br/><br/>";
					}
	}

		if($SalesCodeType=="multiple") {
			
			$sql = "select A.ccode, A.cdesc, ifnull(B.ccode,'') as custcode from groupings A left join customers_accts B on A.ccode=B.citemtype and B.ccode='$cCustCode' where A.compcode='$company' and ctype='ITEMTYP' and cstatus='ACTIVE' order by cdesc";
            $result=mysqli_query($con,$sql);
             
			//echo  "select A.ccode, A.cdesc, ifnull(B.ccode,'') as custcode from groupings A left join customers_accts B on A.ccode=B.citemtype and B.ccode='$cCustCode' where A.compcode='$company' and ctype='ITEMTYP' and cstatus='ACTIVE' order by cdesc";
			              
         while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
				//echo "<br>".$row['custcode'];
				if($row['custcode']==""){
					$citemtype = $row['ccode'];
					$cacctno = $_REQUEST['txtsalesacctDID'.$citemtype];
					
				mysqli_query($con,"INSERT INTO customers_accts(`compcode`, `ccode`, `citemtype`, `cacctno`) 
				values('$company', '$cCustCode','$citemtype','$cacctno')");
						if(mysqli_error($con)!=""){
							//printf("Errormessage: %s\n", mysqli_error($con));	
							printf("Error creating customer acct codes: %s\n", mysqli_error($con));	
						}

					
				}else{
					$citemtype = $row['ccode'];
					$cacctno = $_REQUEST['txtsalesacctDID'.$citemtype];

				mysqli_query($con,"Update customers_accts set `cacctno` = '$cacctno' where `compcode` = '$company' and `citemtype` = '$citemtype' and  `ccode` = '$cCustCode'");
						if(mysqli_error($con)!=""){
							//printf("Errormessage: %s\n", mysqli_error($con));	
							printf("Error updating customer acct codes: %s\n", mysqli_error($con));	
						}
					
				}
							
			}
			
		}

	
//cntacts
		$UnitRowCnt = $_REQUEST['hdncontlistcnt'];
		//INSERT CONTACTS IF MERON
		if($UnitRowCnt>=1){
			mysqli_query($con,"DELETE FROM `customers_contacts` where ccode = '$cCustCode'");
			//echo $UnitRowCnt;
			for($z=1; $z<=$UnitRowCnt; $z++){
				$cIConNme = $_REQUEST['txtConNme'.$z];
				$cIConDes = $_REQUEST['txtConDes'.$z];
				$cIConDept = $_REQUEST['txtConDept'.$z];
				$cIConEml = $_REQUEST['txtConeml'.$z];
				$cIConTel = $_REQUEST['txtContel'.$z];
				$cIConMob = $_REQUEST['txtConmob'.$z];
										
				if (!mysqli_query($con, "INSERT INTO `customers_contacts`(`compcode`, `ccode`, `cname`, `cdesignation`, `cdept`, `cemail`, `cphone`, `cmobile`) VALUES ('$company','$cCustCode','$cIConNme','$cIConDes','$cIConDept','$cIConEml','$cIConTel','$cIConMob')")) {
						if(mysqli_error($con)!=""){
							$myerror = "Contact Lists Error: ". mysqli_error($con)."<br/><br/>";
						}
				} 
	
			}
		}
		
		$DelAddrsCnt = $_REQUEST['hdnaddresscnt'];
		//INSERT ADDRESS IF MERON
		if($DelAddrsCnt>=1){
			mysqli_query($con,"DELETE FROM `customers_address` where ccode = '$cCustCode'");
			//echo $UnitRowCnt;
			for($z=1; $z<=$DelAddrsCnt; $z++){
				$cDelAddNo = $_REQUEST['txtdeladdno'.$z];
				$cDelAddCt = $_REQUEST['txtdeladdcity'.$z]; 
				$cDelAddSt = $_REQUEST['txtdeladdstt'.$z];
				$cDelAddCr = $_REQUEST['txtdeladdcntr'.$z];
				$cDelAddZp = $_REQUEST['txtdeladdzip'.$z];
										
				if (!mysqli_query($con, "INSERT INTO `customers_address`(`compcode`, `ccode`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`) VALUES ('$company','$cCustCode','$cDelAddNo','$cDelAddCt','$cDelAddSt','$cDelAddCr','$cDelAddZp')")) {
						if(mysqli_error($con)!=""){
							echo "Error Addresses: ".mysqli_error($con);
						}
				} 
	
			}
		}
		
//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'UPDATED','CUSTOMER','$compname','Update Customer Details')");


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
			if (file_exists("../../imgcust/" . $_FILES["file"]["name"])) {
				unlink ("../../imgcust/" . $_FILES["file"]["name"]);
			}
			
				$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
				$targetPath = "../../imgcust/".$_FILES['file']['name']; // Target path where file is to be stored
				
				$newtargetPath = "../../imgcust/".$cCustCode.".".$file_extension; // to rename the image to userid
				move_uploaded_file($sourcePath,$newtargetPath) ; // Moving Uploaded file
				//echo "\nImage Uploaded Successfully...!!";
				//echo "\nFile Name: " . $newtargetPath;
				//echo "\nFile Type: " . $_FILES["file"]["type"];
				//echo "\nFile Size: " . ($_FILES["file"]["size"] / 1024) . " kB";

				//update file name in users table
				if (!mysqli_query($con, "UPDATE customers set cuserpic = '$newtargetPath' where compcode='$company' and `cempid`='$cCustCode'")) {
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
