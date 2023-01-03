<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if ($_REQUEST['y'] <> "") {
		$salesno = str_replace(",","','",$_REQUEST['y']);
		
		$qry = " and ctranno not in ('".$salesno."') ";
	}
	else {
		$qry = " ";
	}
	
	//"select * from sales where compcode='$company' and lapproved=1 and ccode='".$_POST['x']."'".$qry."order by dcutdate desc, ctranno desc"
	
	$sqlqry = "select A.ctranno, A.dbegin, A.dend, A.npayamt, A.ndedamt, X.npaidamt, Y.acctno, Y.ctitle 
	from loans A 
	LEFT join
		(
			Select A.cloanno, sum(A.namount) as npaidamt From receipt_loans_t A 
			left Join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			left join loans C on A.compcode=C.compcode and A.ctranno=C.ctranno
			where A.compcode='$company' and B.lapproved=1 and C.ccode='".$_REQUEST['x']."'
			Group By A.cloanno
		) X on A.ctranno = X.cloanno
		left join 
    	(
        	Select A.crefno, C.acctno, C.ctitle, C.ndebit 
            from apv_d A 
            left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
            left join glactivity C on A.compcode=C.compcode and A.ctranno=C.ctranno 
            Where A.compcode='001' and B.captype = 'Loans' and C.ndebit<>0 and B.ccode='80050'
        ) Y on A.ctranno=Y.crefno
	where A.compcode='$company' and A.lapproved=1 and A.ccode='".$_REQUEST['x']."' ".$qry." 
	order by A.dcutdate desc, A.ctranno desc";
	$result = mysqli_query ($con, $sqlqry);
	
	//echo $sqlqry;

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$ngross = $row['npayamt'];
		$npaid = $row['npaidamt'];
		
		$ntotal = ((float)$ngross - (float)$npaid);

		if((float)$ntotal > 0)
		{
			
			 $json['ctranno'] = $row['ctranno'];
			 $json['dbegin'] = $row['dbegin'];
			 $json['dend'] = $row['dend'];
			 $json['npaymnt'] = $row['npayamt'];
			 $json['ndedamt'] = $row['ndedamt'];
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
