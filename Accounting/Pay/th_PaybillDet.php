<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$ccvno = $_REQUEST['x'];

	$dtrandate = "";
	$result = mysqli_query ($con,"Select dtrandate from paybill where compcode='$company' and ctranno='$ccvno'");
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$dtrandate = $row['dtrandate'];
		}
	}

		$mysql = "Select A.cacctno, A.crefrr, a.capvno, DATE_FORMAT(a.dapvdate,'%m/%d/%Y') as dapvdate, a.namount, a.nowed, a.napplied, IFNULL(b.npayed,0) as npayed, c.cacctdesc
		From paybill_t a
		left join
			(
				select x.capvno, sum(x.napplied) as npayed
				from paybill_t x left join paybill y on x.compcode=y.compcode and x.ctranno=y.ctranno
				where x.compcode = '$company' and y.lcancelled=0 and x.ctranno <> '$ccvno' and y.dtrandate <= '$dtrandate'
				group by x.capvno
			) b on a.capvno=b.capvno
		left join accounts c on a.compcode=c.compcode and a.cacctno=c.cacctid 
		where a.compcode='$company' and a.ctranno='$ccvno'
		order by a.nident";

		$result = mysqli_query ($con, $mysql); 
		
	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
	
			 $json['cacctno'] = $row['cacctno']; 
			 $json['cacctdesc'] = $row['cacctdesc'];
			 $json['capvno'] = $row['capvno'];
			 $json['crefrr'] = $row['crefrr'];
			 $json['dapvdate'] = $row['dapvdate'];
			 $json['namount'] = $row['namount'];
			 $json['nowed'] = $row['nowed'];
			 $json['napplied'] = $row['napplied'];
			 $json['npayed'] = $row['npayed'];
			 $json2[] = $json;
	
		}
	}
	
	echo json_encode($json2);


?>
