<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$code = $_REQUEST['code'];
	
	$result = mysqli_query ($con, "SELECT A.ctranno, DATE_FORMAT(B.dapvdate,'%m/%d/%Y') as dapvdate, sum(A.ncredit) as namount, IFNULL(sum(D.napplied),0) as napplied, A.cacctno, C.cacctdesc
	FROM `apv_t` A 
	left join `apv` B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join `accounts` C on A.compcode=C.compcode and A.cacctno=C.cacctid
	left join `accounts_default` D on A.compcode=D.compcode and A.cacctno=D.cacctno
	left join 
		(	
			select a.napplied, a.capvno, a.ctranno
			from paybill_t a
			left join paybill b on a.ctranno=b.ctranno
			where b.lcancelled=0
		) D on A.ctranno=D.capvno
	where A.compcode='$company' and B.lapproved=1 and A.ncredit <> 0 and D.ccode='PAYABLES' and B.ccode='$code'
	group by A.cacctno,a.ctranno,b.dapvdate order by B.dapvdate"); 

	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
	
			 $json['ctranno'] = $row['ctranno'];
			 $json['dapvdate'] = $row['dapvdate'];
			 $json['namount'] = $row['namount'];
			 $json['napplied'] = $row['napplied'];
			 $json['cacctno'] = $row['cacctno'];
			 $json['cacctdesc'] = $row['cacctdesc'];
			 
			 $json2[] = $json;
	
		}
	}else{
			$json['ctranno'] = "NO";
			
			$json2[] = $json;
	}
	
	echo json_encode($json2);


?>
