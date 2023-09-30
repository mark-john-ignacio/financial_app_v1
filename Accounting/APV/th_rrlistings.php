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

	//if($_REQUEST['typ']=="PurchAdv"){
	//	$qry = $qry." and E.ladvancepay=1";
//	}

//	if($_REQUEST['typ']=="Purchases"){
		$qry = $qry." and IFNULL(E.ladvancepay,0)=0";
//	}

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
	$resultcm = mysqli_query ($con, "Select crefsi, IFNULL(sum(A.ngross),0) as ncm from apadjustment A Where A.compcode='$company' and A.lapproved=1  and lvoid=0 Group By crefsi");
	if(mysqli_num_rows($resultcm)!=0){
		while($rowpayref = mysqli_fetch_array($resultcm, MYSQLI_ASSOC)){
			$nCM[] = $rowpayref; 
		}
	}

	$arrRRLISTING = array();
	$qryres = "select A.ctranno, sum(A.namount) as ngross, C.cacctid, C.cacctdesc, IFNULL(A.cewtcode,0) as cewtcode, IFNULL(A.newtrate,0) as newtrate, A.cvatcode, A.nrate, ifnull(B.crefsi,'') as crefsi, E.ladvancepay, B.dreceived
	from suppinv_t A
	left join suppinv B on A.compcode=B.compcode and A.ctranno=B.ctranno 
	left join accounts C on B.compcode=C.compcode and B.ccustacctcode=C.cacctno 
	left join suppliers D on B.compcode=D.compcode and B.ccode=D.ccode 
	left join purchase E on A.compcode=E.compcode and A.crefPO=E.cpono 
	where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and B.ccode='".$_REQUEST['cust']."'". $qry ." 
	Group By A.ctranno, C.cacctid, C.cacctdesc, IFNULL(A.cewtcode,0), A.cvatcode, 
	A.nrate, ifnull(B.crefsi,''), B.dreceived";

	//echo $qryres;
	
	$result = mysqli_query($con, $qryres); 
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrRRLISTING[] = $row;
		}
	}

	$cntr = 0;
		foreach($arrRRLISTING as $row){

			if(!in_array($row['ctranno'], $arrrefinvx)){

				//ewt compute
				$nnet = floatval($row['ngross']);
				$nvatamt = 0;
				if(floatval($row['nrate'])!==0){
					$nnet = floatval($row['ngross']) / (1+(floatval($row['nrate'])/100));
					$nvatamt = floatval($row['ngross']) - round($nnet,2);
				}

				$newtamt = 0;
				if(floatval($row['newtrate'])!==0){
					$newtamt = $nnet * (floatval($row['newtrate'])/100);
				}

				//allcm
				$xcm = 0;
				foreach($nCM as $row90){
					if($row90['crefsi']==$row['ctranno']){
						$xcm = $row90['ncm'];
					}
				}

				$cntr = $cntr + 1;
				$json['crrno'] = $row['ctranno'];
				$json['ngross'] = $row['ngross'];
				$json['napplied'] = 0;
				$json['ncm'] = $xcm;
				$json['vatyp'] = $row['cvatcode'];
				$json['vatrte'] = $row['nrate'];
				$json['vatamt'] = round($nvatamt,2);
				$json['nnetamt'] = round($nnet,2);
				$json['newtamt'] = round($newtamt,2);
				$json['cewtcode'] = $row['cewtcode'];
				$json['newtrate'] = $row['newtrate'];
				$json['ddate'] = $row['dreceived'];
						
				$json['cremarks'] = "";
				
				$json['cacctno'] = $row['cacctid'];
				$json['ctitle'] = $row['cacctdesc']; 
				$json['crefsi'] = $row['crefsi']; 
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
