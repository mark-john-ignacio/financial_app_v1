<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$myqry = "select A.ngross,B.cvattype,B.nvatrate,ifnull(B.newtcode,'') as newtcode,CASE WHEN ifnull(B.newtcode,'') = '' THEN 0 Else C.nrate End as nrate,C.cbase from receive A left join suppliers B on A.ccode=B.ccode LEFT JOIN wtaxcodes C ON B.newtcode = C.ctaxcode where A.compcode='$company' and A.ctranno = '".$_REQUEST['rrid']."'";
	$result = mysqli_query ($con, $myqry);
	
	//echo $myqry; 
	$npaymnt = 0;
	$nCM = 0;
	if(mysqli_num_rows($result)!=0){
		//get payments
		$qrypaymnt = "Select A.compcode, B.ccode, sum(A.napplied) as napplied from apv_d A left join apv B on A.ctranno=B.ctranno Where A.compcode='$company' and A.crefno = '".$_REQUEST['rrid']."' and B.lcancelled=0 Group By A.compcode,B.ccode";
		$resultpay = mysqli_query ($con, $qrypaymnt);
		if(mysqli_num_rows($resultpay)!=0){
			$row = mysqli_fetch_array($resultpay, MYSQLI_ASSOC);
			$npaymnt = $row['napplied'];
		}
		
		//get CMs
		$qrycm = "Select ifnull(sum(B.namount),0) as ncm from apcm A left join purchreturn_t B on A.compcode=B.compcode and A.crefno=B.ctranno Where A.compcode='$company' and B.creference = '".$_REQUEST['rrid']."' and A.lapproved=1";
		
		$resultcm = mysqli_query ($con, $qrycm);
		if(mysqli_num_rows($resultcm)!=0){
			$rowCM = mysqli_fetch_array($resultcm, MYSQLI_ASSOC);
			$nCM = $rowCM['ncm'];
		}
		
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$vatamt = 0;
			$vatnet = $row['ngross'];
			$ewtamt = 0;
			
			if($row['nvatrate']<>0){
				$vatnet = floatval($row['ngross']) / floatval(1 + (floatval($row['nvatrate'])/100));
				$vatamt = floatval($row['ngross']) - floatval($vatnet); 
			}
			
			$ewtamt = floatval($vatnet)*(floatval($row['nrate'])/100);
			
			 $json['ngross'] = $row['ngross'];
			 $json['vatval'] = $vatamt;
			 $json['vatnet'] = $vatnet;
			 $json['ewtcode'] = $row['newtcode'];
			 $json['ewtrate'] = $row['nrate'];
			 $json['ewtamt'] = $ewtamt;
			 $json['npaymnt'] = $npaymnt;
			 $json['vatcode'] = $row['cvattype']; 
			 $json['vatrate'] = $row['nvatrate']; 
			 $json['ncm'] = $nCM;
			 $json2[] = $json;
	
		}
	}
	
	echo json_encode($json2);


?>
