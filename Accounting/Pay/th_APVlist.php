<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$typ = $_REQUEST['typ'];

	//ewt and vat accts PURCH_VAT EWTPAY
	$disreg = array();
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
	}


	$rfplist = array();
	$sql = "Select capvno from rfp where compcode='$company' and lapproved = 1";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$rfplist[] = $row['capvno'];
	}

	
	if($typ=="apv"){

		$sql="SELECT A.ctranno, B.crefno, DATE_FORMAT(A.dapvdate,'%m/%d/%Y') as dapvdate, sum(B.ncredit) as namount, IFNULL(sum(D.napplied),0) as napplied, B.cacctno, C.cacctdesc
		FROM `apv` A
		left join 
			(
				Select compcode, ctranno, crefno, sum(ndue) as ncredit, cacctno
				From apv_d
				Group by ctranno, crefno, cacctno
				
				UNION ALL 
				
				Select G.compcode, G.ctranno, '' as crefno, sum(G.ncredit) as ncredit, G.cacctno
				From apv_t G left join apv H on G.compcode=H.compcode and G.ctranno=H.ctranno
				Where G.compcode='$company' and H.captype='Others' and G.ncredit <> 0 and G.cacctno not in ('".implode("','",$disreg)."')
				Group by G.ctranno, G.cacctno
			) B on A.compcode=B.compcode and A.ctranno=B.ctranno
		left join `accounts` C on B.compcode=C.compcode and B.cacctno=C.cacctid
		left join 
			(	
				select sum(a.napplied) as napplied, a.capvno, a.cacctno
				from paybill_t a
				left join paybill b on a.ctranno=b.ctranno
				where b.lcancelled=0
				group by a.capvno, a.cacctno
			) D on  A.ctranno=D.capvno and B.cacctno=D.cacctno
		where A.compcode='$company' and A.lapproved=1 and B.ncredit <> 0 and C.ccategory='LIABILITIES' and A.ccode='$code'
		group by B.cacctno,B.crefno,A.ctranno,A.dapvdate order by A.dapvdate";

		/*
		$sql = "SELECT A.ctranno, B.crefno, DATE_FORMAT(B.dapvdate,'%m/%d/%Y') as dapvdate, sum(A.ncredit) as namount, IFNULL(sum(D.napplied),0) as napplied, A.cacctno, C.cacctdesc
		FROM `apv_t` A 
		left join `apv` B on A.compcode=B.compcode and A.ctranno=B.ctranno
		left join `accounts` C on A.compcode=C.compcode and A.cacctno=C.cacctid
		left join 
			(	
				select sum(a.napplied) as napplied, a.capvno, a.cacctno
				from paybill_t a
				left join paybill b on a.ctranno=b.ctranno
				where b.lcancelled=0
				group by a.capvno, a.cacctno
			) D on  A.ctranno=D.capvno and A.cacctno=D.cacctno
		where A.compcode='$company' and B.lapproved=1 and A.ncredit <> 0 and C.ccategory='LIABILITIES' and B.ccode='$code'
		group by A.cacctno,a.ctranno,b.dapvdate order by B.dapvdate";
		*/
	}elseif($typ=="po"){

		$sql = "SELECT A.cpono as ctranno, '' as crefno, DATE_FORMAT(A.ddate,'%m/%d/%Y') as dapvdate, sum(A.ndueamt) as namount, IFNULL(sum(D.napplied),0) as napplied, '' as cacctno, '' as cacctdesc
		FROM `purchase` A 
		left join 
			(	
				select a.napplied, a.capvno, a.ctranno
				from paybill_t a
				left join paybill b on a.ctranno=b.ctranno
				where b.lcancelled=0
			) D on A.cpono=D.capvno
		where A.compcode='$company' and A.ladvancepay = 1 and A.lapproved=1 and A.ccode='$code'
		order by A.ddate";
	}
	
	$result = mysqli_query ($con, $sql); 

	$json = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

			$isyes = "True";
			if($typ=="apv"){
				if(!in_array($row['ctranno'], $rfplist)){
					$isyes = "False";
				}
			}

			if($isyes=="True"){
			
				$remain = floatval($row['namount']) - floatval($row['napplied']);

				if($remain>0){
		
					$json['ctranno'] = $row['ctranno'];
					$json['crefno'] = $row['crefno'];
					$json['dapvdate'] = $row['dapvdate'];
					$json['namount'] = number_format($row['namount'],2);
					$json['napplied'] = $row['napplied'];

					if($typ=="apv"){
						$json['cacctno'] = $row['cacctno'];
						$json['cacctdesc'] = $row['cacctdesc'];
					}elseif($typ=="po"){
							//get default acct code for advance payments
							$readvcode = mysqli_query ($con, "Select A.cacctno, B.cacctdesc From accounts_default A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid Where A.compcode='$company' and A.ccode='PO_ADV_PAYMENT'");
							while($rowadv = mysqli_fetch_array($readvcode, MYSQLI_ASSOC)){
								$json['cacctno'] = $rowadv['cacctno'];
								$json['cacctdesc'] = $rowadv['cacctdesc'];
							}
					}
					
					$json2[] = $json;

				}

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
