<?php
session_start();
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
		
	$sql = "select A.ctranno, A.dbegin, A.dend, A.npayamt, A.ndedamt, X.npaidamt, Y.cacctno, Y.ctitle  
	from loans A 
	LEFT join
		(
			Select A.cloanno, sum(A.namount) as npaidamt From receipt_loans_t A 
			left Join receipt B on A.compcode=B.compcode and A.ctranno=B.ctranno
			left join loans C on A.compcode=C.compcode and A.ctranno=C.ctranno
			where A.compcode='$company' and B.lapproved=1 and A.cloanno='".$_REQUEST['id']."'
			Group By A.cloanno
		) X on A.ctranno = X.cloanno
		left join 
    	(
        	Select A.crefno, D.cacctno, D.ctitle, D.ndebit
            from apv_d A 
            left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
            left join apv_t D on A.compcode=D.compcode and A.ctranno=D.ctranno 
            Where A.compcode='001' and B.captype = 'Loans' and D.ndebit<>0 and A.crefno='".$_REQUEST['id']."'
        ) Y on A.ctranno=Y.crefno
	where A.compcode='$company' and A.lapproved=1 and A.ctranno='".$_REQUEST['id']."'
	order by A.dcutdate desc, A.ctranno desc";
	
	$result = mysqli_query ($con, $sql); 
	//echo $sql."<br><br><br>";
	//$json2 = array();
	//$json = [];
	
				//	if (!mysqli_query($result)) {
					//	printf("Errormessage: %s\n", mysqli_error($con));
				//	} 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

		$ngross = $row['npayamt'];
		$npaid = $row['npaidamt'];
		
		$ntotal = ((float)$ngross - (float)$npaid);
			
			 $json['ctranno'] = $row['ctranno'];
			 $json['dbegin'] = $row['dbegin'];
			 $json['dend'] = $row['dend'];
			 $json['namount'] = $row['npayamt'];
			 $json['ndeduct'] = $row['ndedamt'];
			 $json['nbalance'] = $ntotal;
			 $json['cacctno'] = $row['cacctno'];
			 $json['ctitle'] = $row['ctitle'];
			 $json2[] = $json;

	}


if(isset($json2)){
	echo json_encode($json2);
}
else{
	echo "";
}


?>
