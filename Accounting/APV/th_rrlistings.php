<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if($_REQUEST['y']!=""){
		$qry = "and A.ctranno not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}

	if($_REQUEST['typ']!="PettyCash"){

		$qrycust = "and A.ccode='".$_REQUEST['cust']."'";
	}
	else{

		$qrycust = "";
	}

	$qryres = "select A.*, B.cacctdesc, C.cname, D.napplied, C.cvattype, C.nvatrate, E.lcompute,ifnull(A.crefsi,'') as crefsi from suppinv A left join accounts B on A.compcode=B.compcode and A.ccustacctcode=B.cacctno left join suppliers C on A.compcode=C.compcode and A.ccode=C.ccode left join vatcode E on C.compcode=E.compcode and C.cvattype=E.cvatcode left join (Select A.crefno, sum(A.napplied) as napplied from apv_d A left join apv B on A.ctranno=B.ctranno Where A.compcode='$company' and B.lcancelled=0 Group By A.crefno) as D on A.ctranno=D.crefno where A.compcode='$company' ".$qrycust." and A.lapproved=1 ".$qry;
	
	$result = mysqli_query ($con, $qryres); 
	$cntr = 0;
	//echo $qryres;
	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			if(( floatval($row['ngross']) - floatval($row['napplied'])) > 0 ) {
				$cntr = $cntr + 1;
				 $json['crrno'] = $row['ctranno'];
				 $json['ngross'] = $row['ngross'];
				 $json['ddate'] = $row['dreceived'];
			
				if($_REQUEST['typ']=="PettyCash"){
					$json['cremarks'] = $row['cname'];
				}
				else{
					$json['cremarks'] = $row['cremarks'];
				}
			 
				 $json['cacctno'] = $row['ccustacctcode'];
				 $json['ctitle'] = $row['cacctdesc'];
				 $json['vatyp'] = $row['lcompute'];
				 $json['vatrte'] = $row['nvatrate']; 
				 $json['crefsi'] = $row['crefsi']; 
				 $json2[] = $json;
			}
	
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
