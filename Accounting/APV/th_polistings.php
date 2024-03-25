<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$json2 = array();

	@$refpaylistMAIN = array();
	$resrefpay = mysqli_query($con, "Select crefno from apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.captype='PurchAdv' and (B.lvoid=0 and B.lcancelled=0)");
	if(mysqli_num_rows($resrefpay)!=0){
		while($rowpayref = mysqli_fetch_array($resrefpay, MYSQLI_ASSOC)){
			@$refpaylistMAIN[] = $rowpayref['crefno']; 
		}
	}

	if($_REQUEST['y']!=""){
		$qry = "and A.cpono not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	$qrycust = "and B.ccode='".$_REQUEST['cust']."' and B.ccurrencycode='".$_REQUEST['curr']."'";
	
	$arrRRLISTING = array();
	$qryres = "select B.cpono as ctranno, B.ngrossbefore, B.nvat, B.nnet, B.newt, B.ngross, B.nbasegross, C.cacctid, C.cacctdesc, '' as crefsi, DATE(B.ddate) as dreceived
	From purchase B
	left join accounts C on B.compcode=C.compcode and B.ccustacctcode=C.cacctno 
	left join suppliers D on B.compcode=D.compcode and B.ccode=D.ccode 
	where B.compcode='$company' ".$qrycust." and B.lapproved=1 and B.ladvancepay=1 ".$qry.""; 
	$result = mysqli_query($con, $qryres); 

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrRRLISTING[] = $row;
		}
	}

	$cntr = 0;
	foreach($arrRRLISTING as $row){
		if(!in_array($row['ctranno'],@$refpaylistMAIN)){

			$cntr = $cntr + 1;
			$json['crrno'] = $row['ctranno'];
			$json['ngross'] = $row['ngross'];
			$json['nbasegross'] = $row['nbasegross'];
			$json['ngrossbefore'] = $row['ngrossbefore'];
			$json['ncm'] = 0;			
			$json['nvat'] = $row['nvat'];
			$json['nnet'] = $row['nnet'];
			$json['newt'] = $row['newt'];
			$json['ddate'] = $row['dreceived'];
			
			$json['cacctno'] = $row['cacctid'];
			$json['ctitle'] = $row['cacctdesc'];
			$json['crefsi'] = $row['crefsi'];
			$json['nadvpay'] = 1; 
						
			$json2[] = $json;

		}		
	}
	
	echo json_encode($json2);


?>
