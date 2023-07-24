<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];

	//ewt and vat accts PURCH_VAT EWTPAY
	$disreg = array();
	$disregEWT = "";
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
		if($row['ccode']=="EWTPAY"){
      $disregEWT = $row['cacctno'];
    }
	}


	$nRFPvalue = 0;
	$result = mysqli_query($con,"SELECT * FROM `parameters` WHERE compcode='$company' and ccode='RFPMODULE'"); 											
	if (mysqli_num_rows($result)!=0) {
		$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);						 
		$nRFPvalue = $all_course_data['cvalue']; 							
	}

		$rfplist = array();
		$rfplistamt = array();
		$sql = "Select A.ctranno, A.capvno, A.cacctno, A.npayable from rfp_t A left join rfp B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved = 1";
		$result = mysqli_query ($con, $sql); 
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$rfplist[] = $row;
		}

		$sql="SELECT A.ctranno, B.crefno, DATE_FORMAT(A.dapvdate,'%m/%d/%Y') as dapvdate, sum(B.ncredit) as namount, IFNULL(sum(D.napplied),0) as napplied, IFNULL(sum(B.newtamt),0) as newtamt, B.cacctno, C.cacctdesc
		FROM `apv` A
		left join 
			(
				Select compcode, ctranno, crefno, sum(ndue) as ncredit, sum(newtamt) as newtamt, cacctno
				From apv_d
				Group by ctranno, crefno, cacctno
				
				UNION ALL 

				Select X.compcode, X.ctranno, X.crefno, SUM(ncredit), SUM(newtamt), X.cacctno
				From (
					Select G.compcode, G.ctranno, '' as crefno, 
					CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') THEN G.ncredit ELSE 0 END as ncredit, 
					CASE WHEN G.cacctno = '".$disregEWT."' THEN G.ncredit ELSE 0 END as newtamt,
					CASE WHEN G.cacctno not in ('".implode("','",$disreg)."') THEN G.cacctno ELSE null END as cacctno 
					From apv_t G left join apv H on G.compcode=H.compcode and G.ctranno=H.ctranno 
					Where G.compcode='$company' and H.captype='Others' and G.ncredit <> 0 
				) X 
				Group By X.compcode, X.ctranno, X.crefno, X.cacctno

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

	//echo $sql;
	$result = mysqli_query ($con, $sql); 

	$json = array();
	//$json = [];
	$iswith = 0;
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

			$isyes = "False";
			$xdngross = 0;
			$xrefrfp = "";
			$xrefrfpay = 0;
			if($nRFPvalue==1){
				foreach($rfplist as $rs0){
					if($row['ctranno']==$rs0['capvno'] && $row['cacctno']==$rs0['cacctno']){
						$isyes = "True";
						$xrefrfpay = $rs0['npayable'];
						$xrefrfp = $rs0['ctranno'];
					}
				}
			}else{
				$isyes = "True";
			}

			if($isyes=="True"){

				if($nRFPvalue==1){
					$xdngross = $xrefrfpay;
				}else{
					$xdngross = $row['namount'];
				}

				
			
				$remain = floatval($xdngross) - floatval($row['napplied']);

				if($remain>0){
					$iswith++;

					$json['ctranno'] = $row['ctranno'];
					$json['crefno'] = $xrefrfp;
					$json['dapvdate'] = $row['dapvdate'];
					$json['namount'] = number_format($xdngross,2);
					$json['napplied'] = $row['napplied'];
					$json['cacctno'] = $row['cacctno'];
					$json['cacctdesc'] = $row['cacctdesc']; 
					$json['newtamt'] = $row['newtamt'];		
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
	
	if($iswith > 0){
		$key_values = array_column($json2, 'crefno'); 
		array_multisort($key_values, SORT_ASC, $json2);
	}

	echo json_encode($json2);


?>
