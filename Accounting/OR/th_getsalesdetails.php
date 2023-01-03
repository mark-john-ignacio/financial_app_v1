<?php
session_start();
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	//"SELECT ctranno, dcutdate, ngross FROM sales WHERE compcode='$company' and ctranno = '".$_GET['id']."'"
	
	$sql = "select A.ctranno, A.dcutdate, A.ngross, A.nnet, A.nvat, IFNULL(B.namount,0) as nCredit, IFNULL(C.namount,0) as nDebit, IFNULL(D.namount,0) as nPayments, E.acctno, E.ctitle 
	from sales A 
	left join 
		( 
			select X.creference, sum(X.namount) as namount from aradj_t X left join aradj Y on X.compcode=Y.compcode and X.ctranno=Y.ctranno 
			where X.compcode='$company' and Y.lcancelled = 0 and Y.ctype='Credit' 
			GROUP BY X.creference 
		) B on A.ctranno=B.creference left join 
		( 
			select U.creference, sum(U.namount) as namount from aradj_t U left join aradj V on U.compcode=V.compcode and U.ctranno=V.ctranno 
			where U.compcode='$company' and V.lcancelled = 0 and V.ctype='Debit' 
			GROUP BY U.creference 
		) C on A.ctranno=C.creference left join
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
