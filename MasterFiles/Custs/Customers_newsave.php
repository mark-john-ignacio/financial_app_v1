<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$cvalcracct = "";
  	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='INCOME_ACCOUNT'");
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);																				
		$cvalcracct = $all_course_data['cvalue']; 																					
	}
	else{
		$cvalcracct = "";
	}

	function chkgrp($valz) {
		global $con;
		
		if($valz==''){
			return "NULL";
		}else{
			return "'".mysqli_real_escape_string($con, $valz)."'";
		}
	}

	$cCustCode = strtoupper($_REQUEST['txtccode']);
	
	$cCustName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc'])); 
	$cTradeName = mysqli_real_escape_string($con, strtoupper($_REQUEST['txttradename']));
	
	$SalesCodeType = $_REQUEST['selaccttyp'];
	
	$CustTyp = $_REQUEST['seltyp'];
	$CustCls = $_REQUEST['selcls'];
	$CreditLimit = $_REQUEST['txtclimit'];
	//$CrripplesLimit = 0;
	$PriceVer = $_REQUEST['selpricever']; 
	$VatType = $_REQUEST['selvattype']; 
	$Terms = $_REQUEST['selcterms']; 
	$Tin = $_REQUEST['txtTinNo'];
	
	$HouseNo = chkgrp($_REQUEST['txtchouseno']);
	$City = chkgrp($_REQUEST['txtcCity']);
	$State = chkgrp($_REQUEST['txtcState']);
	$Country = chkgrp($_REQUEST['txtcCountry']);
	$ZIP = chkgrp($_REQUEST['txtcZip']);
	
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

	//$cparent = chkgrp($_REQUEST['txtcparentD']);  
	$csman = chkgrp($_REQUEST['txtsmanD']);  	
	$preparedby = $_SESSION['employeeid'];
	
	//if($SalesCodeType=="single") {
		$SalesCode = $_REQUEST['txtsalesacctDID'];
		$SalesEXCode = $_REQUEST['txtsalesacctEXDID'];
	//}else{
		//$SalesCode = "";
	//}

	if($cvalcracct=="customer"){
		$SalesCodeCR = $_REQUEST['txtsalesacctDIDCR'];
		$SalesRetCodeCR = $_REQUEST['txtsalesacctRetDIDCR'];
	}else{
		$SalesCodeCR = "";
		$SalesRetCodeCR = "";
	}

	$SelCurr = $_REQUEST['selcurrncy'];

	//$SalesCodeCR = $_REQUEST['txtsalesacctDIDCR']; , `cacctcodesalescr` , '$SalesCodeCR' , `cparentcode` , $cparent
	
	//INSERT NEW ITEM
	if(!mysqli_query($con,"INSERT INTO `customers`(`compcode`, `cempid`, `cname`, `ctradename`, `cacctcodesales`, `cacctcodesalesex`, `cacctcodetype`,`ccustomertype`, `ccustomerclass`, `cpricever`, `cvattype`, `cterms`, `ctin`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`, `cstatus`, `nlimit`, `csman`, `cGroup1`, `cGroup2`, `cGroup3`, `cGroup4`, `cGroup5`, `cGroup6`, `cGroup7`, `cGroup8`, `cGroup9`, `cGroup10`, `cdefaultcurrency`, `cacctcodesalescr`, `cacctcodesalescreturn`) VALUES ('$company', '$cCustCode', '$cCustName', '$cTradeName', '$SalesCode', '$SalesEXCode', '$SalesCodeType','$CustTyp', '$CustCls', '$PriceVer', '$VatType', '$Terms', '$Tin', $HouseNo, $City, $State, $Country, $ZIP, 'ACTIVE', '$CreditLimit', $csman, $cGrp1, $cGrp2, $cGrp3, $cGrp4, $cGrp5, $cGrp6, $cGrp7, $cGrp8, $cGrp9, $cGrp10, '$SelCurr', '$SalesCodeCR', '$SalesRetCodeCR')")){
		if(mysqli_error($con)!=""){
			printf("Errormessage: %s\n", mysqli_error($con));	
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

				
			}
						
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
			$cIConNme = str_replace("'","\'",$_REQUEST['txtConNme'.$z]);
			$cIConDes = str_replace("'","\'",$_REQUEST['txtConDes'.$z]); 
			$cIConDept = str_replace("'","\'",$_REQUEST['txtConDept'.$z]);

									
			if (!mysqli_query($con, "INSERT INTO `customers_contacts`(`compcode`, `ccode`, `cname`, `cdesignation`, `cdept`) VALUES ('$company','$cCustCode','$cIConNme','$cIConDes','$cIConDept')")) {
				echo "Error Contacts: ".mysqli_error($con);
			}else{
				$xcid = mysqli_insert_id($con);

				foreach($arridxcv as $rmnb){
					$xcvlxcz = str_replace("'","\'",$_REQUEST['txtConAdd'.$rmnb.$z]);
					mysqli_query($con, "INSERT INTO `customers_contacts_nos`(`compcode`, `customers_contacts_cid`, `contact_type`, `cnumber`) VALUES ('$company','$xcid','$rmnb','$xcvlxcz')");
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
									
			if (!mysqli_query($con, "INSERT INTO `customers_address`(`compcode`, `ccode`, `chouseno`, `ccity`, `cstate`, `ccountry`, `czip`) VALUES ('$company','$cCustCode','$cDelAddNo','$cDelAddCt','$cDelAddSt','$cDelAddCr','$cDelAddZp')")) {
					if(mysqli_error($con)!=""){
						echo "Error Addresses: ".mysqli_error($con);
					}
			} 

		}
	}

	//INSERT Childs
	$ChildCnt = $_REQUEST['hdnchildcnt'];
	if($ChildCnt>=1){
		$sql = "SELECT norder FROM  customers_secondary where compcode='$company' and cmaincode = '$cCustCode' Order By norder DESC";
		$result = mysqli_query($con, $sql);
		$rowcount=mysqli_num_rows($result);

		if($rowcount>0){
			$row   = mysqli_fetch_row($result);	
			$chilNUM = floatval($row[0]) + 1;			
		}else{
			$chilNUM = "0001";
			$chilNUM = 1;
		}

		//echo $UnitRowCnt;
		for($z=1; $z<=$ChildCnt; $z++){
			$cChildNo = $_REQUEST['txtchildno'.$z];
			$cChildName = $_REQUEST['txtchildname'.$z]; 
			$cChildAdd = $_REQUEST['txtchildadd'.$z];
			$cChildCty = $_REQUEST['txtchildcity'.$z];
			$cChildStt = $_REQUEST['txtchildstate'.$z];
			$cChildCtr = $_REQUEST['txtchildcountry'.$z];
			$cChildZip = $_REQUEST['txtchildzip'.$z];
			$cChildTin = $_REQUEST['txtchildtin'.$z];

			$chilnonxt = str_pad($chilNUM, 4, '0', STR_PAD_LEFT);
			$chilnonxt = $cCustCode."-".$chilnonxt;

			if (!mysqli_query($con, "INSERT INTO `customers_secondary`(`compcode`, `norder`, `cmaincode`, `ccode`, `cname`, `caddress`, `ccity`, `cstate`, `ccountry`, `czip`, `ctin`) VALUES ('$company','$chilNUM','$cCustCode','$chilnonxt','$cChildName','$cChildAdd','$cChildCty','$cChildStt','$cChildCtr','$cChildZip','$cChildTin')")) {
				if(mysqli_error($con)!=""){
					echo "Error Addresses: ".mysqli_error($con);
				}
			} 

			$chilNUM = floatval($chilNUM) + 1;	

		}
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cCustCode','$preparedby',NOW(),'INSERTED','CUSTOMER','$compname','Insert New Customer')");


	echo "True";
?>
