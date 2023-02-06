<?php
session_start();
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if ($_REQUEST['y'] <> "") {
		$salesno = str_replace(",","','",$_REQUEST['y']);
		
		$qry = " and A.ctranno not in ('".$salesno."') ";
	}
	else {
		$qry = " ";
	}

	$tbl = "";
	if($_REQUEST['typ']=="Trade"){
		$tbl = "sales";
	}elseif($_REQUEST['typ']=="Non-Trade"){
		$tbl = "ntsales";
	}
	
	//"select * from sales where compcode='$company' and lapproved=1 and ccode='".$_POST['x']."'".$qry."order by dcutdate desc, ctranno desc"
	
	$sql = "select A.ctranno, A.dcutdate, A.ngross, IFNULL(B.namount,0) as nCredit, IFNULL(C.namount,0) as nDebit, IFNULL(D.namount,0) as nPayments  , E.acctno, E.ctitle 
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
			select S.csalesno, sum(S.namount) as namount from receipt_sales_t S left join receipt T on S.compcode=T.compcode and S.ctranno=T.ctranno 
			where S.compcode='$company' and T.lcancelled = 0
			GROUP BY S.csalesno 
		) D on A.ctranno=D.csalesno
		left join glactivity E on A.compcode=E.compcode and A.ctranno=E.ctranno and E.ndebit <> 0
		left join customers F on A.compcode=F.compcode and A.ccode=F.cempid
	where A.compcode='$company' and A.lapproved=1 and A.ccode='".$_REQUEST['x']."' ".$qry." order by A.dcutdate, A.ctranno";
	
	//echo $sql;
	
	$result = mysqli_query ($con, $sql);
	

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$ngross = $row['ngross'];
		$ndm = $row['nDebit'];
		$ncm = $row['nCredit'];
		$npay = $row['nPayments'];
		
		$ntotal = (((float)$ngross + (float)$ndm) - (float)$ncm) - (float)$npay;

		if((float)$ntotal > 0)
		{
			
			 $json['csalesno'] = $row['ctranno'];
			 $json['dcutdate'] = $row['dcutdate'];
			 $json['ngross'] = number_format($row['ngross'],2);
			 $json['ndebit'] = number_format($row['nDebit'],2);
			 $json['ncredit'] = number_format($row['nCredit'],2);
			 $json['npayment'] = number_format($row['nPayments'],2);
			 $json['cacctno'] = $row['acctno'];
			 $json['ctitle'] = $row['ctitle'];
			 $json2[] = $json;
		 
		}

	}
	
	$sql0 = "select A.ctranno, A.dcutdate, A.ngross, IFNULL(B.namount,0) as nCredit, IFNULL(C.namount,0) as nDebit, IFNULL(D.namount,0) as nPayments  , E.acctno, E.ctitle 
	from sales A 
	left join 
		( 
			select X.creference, sum(X.namount) as namount from aradj_t X left join aradj Y on X.compcode=Y.compcode and X.ctranno=Y.ctranno 
			where X.compcode='$company' and Y.lapproved = 1 and Y.ctype='Credit' 
			GROUP BY X.creference 
		) B on A.ctranno=B.creference left join 
		( 
			select U.creference, sum(U.namount) as namount from aradj_t U left join aradj V on U.compcode=V.compcode and U.ctranno=V.ctranno 
			where U.compcode='$company' and V.lapproved = 1 and V.ctype='Debit' 
			GROUP BY U.creference 
		) C on A.ctranno=C.creference left join
		( 
			select S.csalesno, sum(S.napplied) as namount from receipt_sales_t S left join receipt T on S.compcode=T.compcode and S.ctranno=T.ctranno 
			where S.compcode='$company' and T.lapproved = 1
			GROUP BY S.csalesno 
		) D on A.ctranno=D.csalesno
		left join glactivity E on A.compcode=E.compcode and A.ctranno=E.ctranno and E.ndebit <> 0
		left join customers F on A.compcode=F.compcode and A.ccode=F.cempid
	where A.compcode='$company' and A.lapproved=1 and year(dcutdate)>='2019' and F.cparentcode='".$_REQUEST['x']."' ".$qry." order by A.dcutdate, A.ctranno";
	
	//echo $sql;
	
	$result0 = mysqli_query ($con, $sql0);
	

	while($row0 = mysqli_fetch_array($result0, MYSQLI_ASSOC)){

		$ngross0 = $row0['ngross'];
		$ndm0 = $row0['nDebit'];
		$ncm0 = $row0['nCredit'];
		$npay0 = $row0['nPayments'];
		
		$ntotal0 = (((float)$ngross0 + (float)$ndm0) - (float)$ncm0) - (float)$npay0;

		if((float)$ntotal0 > 0)
		{
			
			 $json['csalesno'] = $row['ctranno'];
			 $json['dcutdate'] = $row['dcutdate'];
			 $json['ngross'] = $row['ngross'];
			 $json['ndebit'] = $row['nDebit'];
			 $json['ncredit'] = $row['nCredit'];
			 $json['npayment'] = $row['nPayments'];
			 $json['cacctno'] = $row['acctno'];
			 $json['ctitle'] = $row['ctitle'];
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
