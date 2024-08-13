<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$json2 = array();

	//get default EWT acct code
	@$ewtpaydef = "";
	@$ewtpaydefdsc = "";
	$gettaxcd = mysqli_query($con,"SELECT A.cacctno, B.cacctdesc FROM `accounts_default` A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.ccode='EWTPAY'"); 
	if (mysqli_num_rows($gettaxcd)!=0) {
		while($row = mysqli_fetch_array($gettaxcd, MYSQLI_ASSOC)){
			@$ewtpaydef = $row['cacctno'];
			@$ewtpaydefdsc = $row['cacctdesc']; 
		}
	}

	@$refpaylistMAIN = array();
	$resrefpay = mysqli_query($con, "Select crefno from apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.captype='EWT' and (B.lvoid=0 and B.lcancelled=0)");
	if(mysqli_num_rows($resrefpay)!=0){
		while($rowpayref = mysqli_fetch_array($resrefpay, MYSQLI_ASSOC)){
			@$refpaylistMAIN[] = $rowpayref['crefno']; 
		}
	}

	if($_REQUEST['y']!=""){
		$qry = "and a.ctranno not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}
	
	$arrRRLISTING = array();
	$qryres = "SELECT SUM(a.ncredit-a.ndebit) as ncredit, a.cewtcode, a.newtrate, a.ctranno
        FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON b.compcode = c.compcode AND b.ccode = c.ccode 
        LEFT JOIN groupings d ON c.compcode = d.compcode AND c.csuppliertype = d.ccode AND d.ctype = 'SUPTYP'				
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) = '$month' AND YEAR(b.dapvdate) = '$year' AND  b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 and a.cacctno='$ewtpaydef' and IFNULL(a.cewtcode,'') <> '' ".$qry." GROUP BY a.cewtcode, a.newtrate, a.ctranno Order By a.cewtcode"; 

	$result = mysqli_query($con, $qryres); 

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$arrRRLISTING[] = $row;
		}
	}

	$cntr = 0;
	foreach($arrRRLISTING as $row){
		if(!in_array($row['ctranno'],@$refpaylistMAIN)){

			$cntr = $cntr + 1;
			$json['ctranno'] = $row['ctranno'];
			$json['ngross'] = $row['ncredit'];			
			$json['cacctno'] = @$ewtpaydef;
			$json['ctitle'] = @$ewtpaydefdsc;
						
			$json2[] = $json;

		}		
	}
	
	echo json_encode($json2);


?>
