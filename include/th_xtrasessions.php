<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

			//Check available checking
			$resavil = mysqli_query($con,"select * from parameters where `compcode` = '$company' and `ccode` = 'INVPOST'");
			$rowavil = mysqli_fetch_assoc($resavil);
			
			//0 = Check ; 1 = Dont Check
			$json['chkinv'] = $rowavil['cvalue'];
			
			//Check VAT setup
			$resvat = mysqli_query($con,"select B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode = '$company'");
			$rowvat = mysqli_fetch_assoc($resvat);
			
			 $json['chkcompvat'] = $rowvat['lcompute'];

			//Check Customer Credit Limit
			$rescrdlmt = mysqli_query($con,"select * from parameters where `compcode` = '$company' and `ccode` = 'CRDLIMIT'");
			$rowcrdlmt = mysqli_fetch_assoc($rescrdlmt);
			
			//0 = Disable ; 1 = Enable
			 $json['chkcustlmt'] = $rowcrdlmt['cvalue'];
			 

			//Check Customer Credit Limit WARNING
			$rescrdlmtwar = mysqli_query($con,"select * from parameters where `compcode` = '$company' and `ccode` = 'CRDLIMWAR'");
			$rowcrdlmtwar = mysqli_fetch_assoc($rescrdlmtwar);
			
			//0 = Accept Warninf ; 1 = Accept Block ; 2 = Refuse Order
			 $json['chklmtwarn'] = $rowcrdlmtwar['cvalue'];

			 
			 $json2[] = $json;
			 echo json_encode($json2);