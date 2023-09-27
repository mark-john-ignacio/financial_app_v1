<?php
session_start();
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$tbl = "";
	if($_REQUEST['typ']=="Trade"){
		$tbl = "sales";
	}elseif($_REQUEST['typ']=="Non-Trade"){
		$tbl = "ntsales";
	}

	//"SELECT ctranno, dcutdate, ngross FROM sales WHERE compcode='$company' and ctranno = '".$_GET['id']."'"
	
	$sql = "select A.ctranno, A.dcutdate, A.ngross, A.nnet, A.nvat, IFNULL(B.namount,0) as nCredit, IFNULL(C.namount,0) as nDebit, IFNULL(D.namount,0) as nPayments, E.acctno, E.ctitle 
	from ".$tbl." A 
	left join 
		( 
			select crefno, sum(ngross) as namount from aradj 
			where compcode='$company' and lapproved = 1 and ctype='Credit' 
			GROUP BY crefno 
		) B on A.ctranno=B.crefno left join 
		( 
			select crefno, sum(ngross) as namount from aradj
			where compcode='$company' and lapproved = 1 and ctype='Debit' 
			GROUP BY crefno 
		) C on A.ctranno=C.crefno left join
		( 
			select S.csalesno, sum(S.napplied) as namount from receipt_sales_t S left join receipt T on S.compcode=T.compcode and S.ctranno=T.ctranno 
			where S.compcode='$company' and T.lcancelled = 0
			GROUP BY S.csalesno 
		) D on A.ctranno=D.csalesno 
	left join glactivity E on A.compcode=E.compcode and A.ctranno=E.ctranno and E.ndebit <> 0
	where A.compcode='$company' and A.lapproved=1 and A.ctranno='".$_REQUEST['id']."'";
	
	$result = mysqli_query ($con, $sql); 
	//echo $sql."<br><br><br>";
	//$json2 = array();
	//$json = [];
	
				//	if (!mysqli_query($result)) {
					//	printf("Errormessage: %s\n", mysqli_error($con));
				//	} 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$ngross = $row['ngross'];
		$ndm = $row['nDebit'];
		$ncm = $row['nCredit'];
		$npay = $row['nPayments'];
		
		//kung may existing payment na.. get ung EWT
		
		$ntotal = (((float)$ngross + (float)$ndm) - (float)$ncm) - (float)$npay;

		if((float)$ntotal > 0)
		{
			
			 $json['csalesno'] = $row['ctranno'];
			 $json['dcutdate'] = $row['dcutdate'];
			 $json['ngross'] = $row['ngross'];
			 $json['ndebit'] = $row['nDebit'];
			 $json['ncredit'] = $row['nCredit'];
			 $json['npayment'] = $row['nPayments'];
			 $json['cacctno'] = $row['acctno'];
			 $json['ctitle'] = $row['ctitle'];
			 $json['nnet'] = $row['nnet'];
			 $json['nvat'] = $row['nvat'];
			 $json2[] = $json;
		 
		}

	}


if(isset($json2)){
	echo json_encode($json2);
}
else{
	echo "";
}


?>
