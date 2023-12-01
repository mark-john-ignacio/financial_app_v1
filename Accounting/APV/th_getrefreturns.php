<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$json2 = array();		

		//get purch ret default acct
		$qrydefacct = "Select A.cacctno, B.cacctdesc From accounts_default A left join accounts B on A.cacctno=B.cacctno where A.compcode='$company' and A.ccode='PURCHRET'";		
		$resultdefacct = mysqli_query ($con, $qrydefacct);
		if(mysqli_num_rows($resultdefacct)!=0){
		
			while($rowdef = mysqli_fetch_array($resultdefacct, MYSQLI_ASSOC)){
				
				$defcode = $rowdef['cacctno'];
				$defcodedesc = $rowdef['cacctdesc'];
				
			}
			
		}

		//get CMs
		$qrycm = "Select A.ctranno, A.dcutdate, B.creference, ifnull(sum(B.namount),0) as ncm from apcm A left join purchreturn_t B on A.compcode=B.compcode and A.crefno=B.ctranno Where A.compcode='$company' and B.creference = '".$_REQUEST['rrid']."' and A.lapproved=1 Group by A.ctranno, B.creference";
		
		$resultcm = mysqli_query ($con, $qrycm);
		if(mysqli_num_rows($resultcm)!=0){
		
			while($row = mysqli_fetch_array($resultcm, MYSQLI_ASSOC)){
			
			 $json['ctranno'] = $row['ctranno'];
			 $json['crefrr'] = $row['creference'];
			 $json['ngross'] = $row['ncm'];
			 $json['ddate'] = $row['dcutdate'];
			 $json['cacctno'] = $defcode;	
			 $json['cacctdesc'] = $defcodedesc;	
			 $json2[] = $json;
	
			}
		}
	
	echo json_encode($json2);


?>
