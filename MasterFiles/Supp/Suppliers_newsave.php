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
	
	$cCustName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	$cTradeName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txttradename']));

	$SalesCode = $_REQUEST['txtsalesacctD'];
	$SalesCodeID = $_REQUEST['txtsalesacctDID'];
	$Type = $_REQUEST['seltyp'];
	$Class = $_REQUEST['selcls'];
	$Terms = $_REQUEST['selterms'];
	$PROCUREMENT = $_REQUEST['procurement'];
	$VatType = $_REQUEST['selvattype'];   
	$VatTypeRate = $_REQUEST['txttaxrate'];
	$VatEWTCode = $_REQUEST['txtewtD']; 
	$Tin = $_REQUEST['txtTinNo'];
	
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);

	//$Contact = chkgrp($_REQUEST['txtcperson']); `cphone`, `cmobile`, `ccontactname`, `cemail`, `cdesignation`
	//$Desig = chkgrp($_REQUEST['txtcdesig']);  $PhoneNo, $Mobile, $Contact, $Email, $Desig
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
	
	$preparedby = $_SESSION['employeeid'];

	$SelCurr = $_REQUEST['selcurrncy'];
	
	//INSERT NEW ITEM
	$sql = "INSERT INTO `suppliers`(`compcode`, `ccode`, `cname`, `ctradename`, `cacctcode`, `cterms`, `csuppliertype`, `csupplierclass`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `cGroup1`, `cGroup2`, `cGroup3`, `cGroup4`, `cGroup5`, `cGroup6`, `cGroup7`, `cGroup8`, `cGroup9`, `cGroup10`, `cvattype`, `ctin`, `nvatrate`, `newtcode`, `cdefaultcurrency`, `procurement`) VALUES ('$company', '$cCustCode', '$cCustName', '$cTradeName', '$SalesCodeID', '$Terms', '$Type', '$Class', $HouseNo, $City, $State, $Country, $ZIP, $cGrp1, $cGrp2, $cGrp3, $cGrp4, $cGrp5, $cGrp6, $cGrp7, $cGrp8, $cGrp9, $cGrp10, '$VatType', '$Tin', '$VatTypeRate', '$VatEWTCode', '$SelCurr', '$PROCUREMENT')";	  


	if (!mysqli_query($con, $sql)) {
		if(mysqli_error($con)!=""){
			echo "Error Main: ".mysqli_error($con);
		}
	}


	$UnitRowCnt = $_REQUEST['hdncontlistcnt'];
		//INSERT CONTACTS IF MERON
		if($UnitRowCnt>=1){
			
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
										
				if (!mysqli_query($con, "INSERT INTO `suppliers_contacts`(`compcode`, `ccode`, `cname`, `cdesignation`, `cdept`) VALUES ('$company','$cCustCode','$cIConNme','$cIConDes','$cIConDept')")) {
					echo "Error Contacts: ".mysqli_error($con);
				}else{
					$xcid = mysqli_insert_id($con);

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
	values('$company', '$cCustCode','$preparedby',NOW(),'INSERTED','SUPPLIER','$compname','Insert New Supplier')");


	echo "True";
?>