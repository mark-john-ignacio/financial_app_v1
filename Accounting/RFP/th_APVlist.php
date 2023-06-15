<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];

		//ewt and vat accts PURCH_VAT EWTPAY
		$disreg = array();
		$sql = "select a.capvno from paybill_t a left join paybill b on a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0";
		$result = mysqli_query ($con, $sql); 
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$disreg[] = $row['capvno'];
		}
	

		$sql="SELECT A.ctranno, A.dapvdate, A.ngross, sum(IFNULL(B.ngross,0)) as npaid, A.cpaymentfor
		FROM `apv` A
		left join rfp B on A.compcode=B.compcode and A.ctranno=B.capvno
		where A.compcode='$company' and A.lapproved=1 and A.ccode='$code'
		group by A.ctranno, A.dapvdate, A.ngross, A.cpaymentfor
		order by A.dapvdate DESC";

		//echo $sql;

	$result = mysqli_query ($con, $sql); 

	$json = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$nbalx = floatval($row['ngross']) - floatval($row['npaid']);

			if($nbalx > 0 && !in_array($row['ctranno'], $disreg)){
	
			 $json['ctranno'] = $row['ctranno'];
			 $json['dapvdate'] = $row['dapvdate'];
			 $json['namount'] = number_format($row['ngross'],2);
			 $json['nbalance'] = number_format($nbalx,2);
			 $json['cpaymentfor'] = $row['cpaymentfor'];
			 
			 $json2[] = $json;

			}
	
		}

		if(count($json)==0){
			$json['ctranno'] = "NO";
			
			$json2[] = $json;
		}
	}else{
			$json['ctranno'] = "NO";
			
			$json2[] = $json;
	}
	
	echo json_encode($json2);


?>
