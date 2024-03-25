<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$qry = "";

	if($_REQUEST['y']!=""){
		$qry = " and A.ctranno not in ('". str_replace(",","','",$_REQUEST['y']) . "') ";
	}
	else{
		$qry = "";
	}

	//all existing suppinv in apv
	$arrrefinvx = array();
	$result = mysqli_query($con, "Select crefno from apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno Where A.compcode='$company' and B.lcancelled=0"); 
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrrefinvx[] = $row['crefno'];
		}
	}

	//get CMs
	$nCM = array();
	$resultcm = mysqli_query ($con, "Select crefsi, IFNULL(sum(A.ngross),0) as ncm from apadjustment A Where A.compcode='$company' and A.lapproved=1 and lvoid=0 and ccode='".$_REQUEST['cust']."' Group By crefsi");
	if(mysqli_num_rows($resultcm)!=0){
		while($rowpayref = mysqli_fetch_array($resultcm, MYSQLI_ASSOC)){
			$nCM[$rowpayref['crefsi']] = $rowpayref['ncm']; 
		}
	}

	$arrRRLISTING = array();
	$qryres = "select B.ctranno, B.ngrossbefore, B.nvat, B.nnet, B.newt, B.ngross, B.nbasegross, C.cacctid, C.cacctdesc, B.crefsi, B.dreceived
	From suppinv B
	left join accounts C on B.compcode=C.compcode and B.ccustacctcode=C.cacctno 
	left join suppliers D on B.compcode=D.compcode and B.ccode=D.ccode 
	where B.compcode='$company' and B.lapproved=1 and B.lvoid=0 and B.ccode='".$_REQUEST['cust']."' and B.ccurrencycode='".$_REQUEST['curr']."'";

	//echo $qryres;
	
	$result = mysqli_query($con, $qryres); 
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrRRLISTING[] = $row;
		}
	}

	$cntr = 0;
	$json2 = array();
	foreach($arrRRLISTING as $row){

		if(!in_array($row['ctranno'], $arrrefinvx)){


			$cntr = $cntr + 1;
			$json['crrno'] = $row['ctranno'];
			$json['ngross'] = $row['ngross'];
			$json['nbasegross'] = $row['nbasegross'];
			$json['ngrossbefore'] = $row['ngrossbefore'];
			$json['ncm'] = ((isset($nCM[$row['ctranno']])) ? $nCM[$row['ctranno']] : 0);			
			$json['nvat'] = $row['nvat'];
			$json['nnet'] = $row['nnet'];
			$json['newt'] = $row['newt'];
			$json['ddate'] = $row['dreceived'];
			
			$json['cacctno'] = $row['cacctid'];
			$json['ctitle'] = $row['cacctdesc']; 
			$json['crefsi'] = $row['crefsi']; 
			$json['nadvpay'] = 0; 
						
			$json2[] = $json;

		}
		
	}
	
	echo json_encode($json2);


?>
