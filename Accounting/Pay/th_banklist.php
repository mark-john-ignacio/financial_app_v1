<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	if($_REQUEST['id']=="cheque"){	
	
		$result = mysqli_query ($con, "SELECT B.nidentity, A.ccode, A.cname, A.cbankacctno, IFNULL(B.ccheckno,'') as ccheckno, IFNULL(B.ccurrentcheck, '') as ccurrentcheck, A.cacctno, C.cacctdesc
		FROM `bank` A left join `bank_check` B on A.compcode=B.compcode and A.ccode=B.ccode
		left join `accounts` C on A.compcode=C.compcode and A.cacctno=C.cacctid
		WHERE A.compcode='$company'"); 

		// and A.ccurrentcheck < A.ccheckto

		//$json2 = array();
		//$json = [];
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		
				$json['ccode'] = $row['ccode'];
				$json['cname'] = $row['cname'];
				$json['cbankacctno'] = $row['cbankacctno'];
				$json['ccheckno'] = $row['ccheckno'];
				$json['ccurrentcheck'] = $row['ccurrentcheck'];
				$json['cacctno'] = $row['cacctno'];
				$json['cacctdesc'] = $row['cacctdesc'];
				$json['nidentity'] = $row['nidentity'];
				$json2[] = $json;
		
			}
		}

	}else{

		$result = mysqli_query ($con, "SELECT A.ccode, A.cname, A.cbankacctno, A.cacctno, C.cacctdesc
		FROM `bank` A left join `accounts` C on A.compcode=C.compcode and A.cacctno=C.cacctid
		WHERE A.compcode='$company'"); 

		// and A.ccurrentcheck < A.ccheckto

		//$json2 = array();
		//$json = [];
		if(mysqli_num_rows($result)!=0){
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
		
				$json['ccode'] = $row['ccode'];
				$json['cname'] = $row['cname'];
				$json['cbankacctno'] = $row['cbankacctno'];
				$json['cacctno'] = $row['cacctno'];
				$json['cacctdesc'] = $row['cacctdesc'];
				$json2[] = $json;
		
			}
		}


	}
	
	echo json_encode($json2);


?>
