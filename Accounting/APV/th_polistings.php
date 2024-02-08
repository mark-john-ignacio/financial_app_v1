<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
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

	$qrycust = "and E.ccode='".$_REQUEST['cust']."' and E.ccurrencycode='".$_REQUEST['curr']."'";
	
	$arrRRLISTING = array();
	$qryres = "select A.cpono as ctranno, SUM(A.namount) as ngross, E.dneeded as dreceived, A.ctaxcode as cvatcode, 
	A.nrate as nvatrate, A.cewtcode, IFNULL(A.newtrate,0) as newtrate, B.cacctid, B.cacctdesc, E.ladvancepay
	from purchase_t A 
	left join purchase E on A.compcode=E.compcode and A.cpono=E.cpono
	left join suppliers C on E.compcode=C.compcode and E.ccode=C.ccode 
	left join accounts B on E.compcode=C.compcode and B.cacctno=C.cacctcode 
	where A.compcode='$company' ".$qrycust." and E.lapproved=1 and E.ladvancepay=1 ".$qry."
	Group By A.cpono, E.dneeded, A.ctaxcode, 
	A.nrate, A.cewtcode, A.newtrate, B.cacctid, B.cacctdesc, E.ladvancepay";
	$result = mysqli_query($con, $qryres); 

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrRRLISTING[] = $row;
		}
	}

	$cntr = 0;
		foreach($arrRRLISTING as $row){

				if(!in_array($row['ctranno'],@$refpaylistMAIN)){

					//ewt compute
					$nnet = floatval($row['ngross']);
					$nvatamt = 0;
					if(floatval($row['nvatrate'])!==0){
						$nnet = floatval($row['ngross']) / (1+(floatval($row['nvatrate'])/100));
						$nvatamt = floatval($row['ngross']) - round($nnet,2);
					}

					$newtamt = 0;
					if(floatval($row['newtrate'])!==0){
						$newtamt = $nnet * (floatval($row['newtrate'])/100);
					}

					$cntr = $cntr + 1;

					$json['crrno'] = $row['ctranno'];
					$json['ngross'] = $row['ngross'];
					$json['napplied'] = 0;
					$json['ncm'] = 0;
					$json['vatyp'] = $row['cvatcode'];
					$json['vatrte'] = $row['nvatrate'];
					$json['vatamt'] = round($nvatamt,2);
					$json['nnetamt'] = round($nnet,2);
					$json['newtamt'] = round($newtamt,2);
					$json['cewtcode'] = $row['cewtcode'];
					$json['newtrate'] = $row['newtrate'];
					$json['ddate'] = $row['dreceived'];
							
					$json['cremarks'] = "";
					
					$json['cacctno'] = $row['cacctid'];
					$json['ctitle'] = $row['cacctdesc']; 
					$json['crefsi'] = ''; 
					$json['nadvpay'] = $row['ladvancepay'];

					$json2[] = $json;

				}		
	}
	
	if($cntr<=0){
			 $json['crrno'] = "NONE";
			 $json['ngross'] = "";
			 $json['ddate'] = "";
			 $json['cremarks'] = "";
			 $json['cacctno'] = "";
			 $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
