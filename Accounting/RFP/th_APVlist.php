<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];

		//ewt and vat accts PURCH_VAT EWTPAY
		$EWTVATS = array();
		$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
		$result = mysqli_query ($con, $sql); 
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$EWTVATS[] = $row['cacctno'];
		}


		
		$disreg = array();
		$sql = "select a.capvno from paybill_t a left join paybill b on a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0";
		$result = mysqli_query ($con, $sql); 
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$disreg[] = $row['capvno'];
		}
	

		$sql="SELECT A.ctranno, B.dapvdate, A.cacctno, D.cacctdesc, sum(A.ncredit) as ncredit, B.ccurrencycode, sum(IFNULL(C.ngross,0)) as npaid, B.cpaymentfor
		FROM `apv_t` A
		left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
		left join 
			(
				Select A.compcode, A.ctranno, A.capvno, A.cacctno, A.npayable as ngross
				From rfp_t A left join rfp B on A.compcode=B.compcode and A.ctranno=B.ctranno
				Where A.compcode='$company' and B.lcancelled=0 and B.lvoid=0
			) C on B.compcode=C.compcode and A.ctranno=C.capvno and A.cacctno=C.cacctno
		left join accounts D on A.compcode=D.compcode and A.cacctno=D.cacctid 
		where A.compcode='$company' and B.lapproved=1  and B.lvoid=0 and B.ccode='$code'
		and D.ccategory='LIABILITIES' and A.ncredit > 0 and A.cacctno not in ('".implode("','",$EWTVATS)."') 
		group by A.ctranno, B.dapvdate, A.cacctno, D.cacctdesc, B.cpaymentfor, B.ccurrencycode
		order by B.dapvdate DESC";

		//echo $sql;

	$result = mysqli_query ($con, $sql); 

	$json = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$nbalx = floatval($row['ncredit']) - floatval($row['npaid']);

			if($nbalx > 0 && !in_array($row['ctranno'], $disreg)){
	
			 $json['ctranno'] = $row['ctranno'];
			 $json['cacctno'] = $row['cacctno'];
			 $json['cacctdesc'] = $row['cacctdesc'];
			 $json['dapvdate'] = $row['dapvdate'];
			 $json['namount'] = number_format($row['ncredit'],2);
			 $json['nbalance'] = number_format($nbalx,2);
			 $json['cpaymentfor'] = $row['cpaymentfor']; 
			 $json['ccurrencycode'] = $row['ccurrencycode']; 

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
