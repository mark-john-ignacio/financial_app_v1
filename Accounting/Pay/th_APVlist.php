<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	$typ = $_REQUEST['typ'];
	
	if($typ=="apv"){
		$sql = "SELECT A.ctranno, DATE_FORMAT(B.dapvdate,'%m/%d/%Y') as dapvdate, sum(A.ncredit) as namount, IFNULL(sum(D.napplied),0) as napplied, A.cacctno, C.cacctdesc
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
	}elseif($typ=="po"){

		$sql = "SELECT A.cpono as ctranno, DATE_FORMAT(A.ddate,'%m/%d/%Y') as dapvdate, sum(A.ngross) as namount, IFNULL(sum(D.napplied),0) as napplied, '' as cacctno, '' as cacctdesc
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
			
			$remain = floatval($row['namount']) - floatval($row['napplied']);

			if($remain>0){
	
			 $json['ctranno'] = $row['ctranno'];
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
