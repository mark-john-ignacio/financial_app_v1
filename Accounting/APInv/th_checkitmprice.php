<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
		 
	$itm = $_REQUEST['itm'];
	$itmunit = $_REQUEST['cunit'];
	$custver = $_REQUEST['cust'];
	$dte = date("Y-m-d");	
	 
	//$sql = "Select A.nprice
	//from items_purch_cost_t A left join items_purch_cost B on A.compcode=B.compcode and A.ctranno=B.ctranno
	//where A.compcode='$company' and B.ccode='$custver' and B.deffectdate <= '$dte' and A.citemno='$itm' and A.cunit='$itmunit' 
	//order by B.deffectdate DESC, B.ddate DESC LIMIT 1";
	//echo $sql;
	
	$sql = "Select A.nprice
	From (
	Select A.nprice, A.cunit, A.nfactor, C.nmarkup, B.dreceived from
	suppinv_t A
	left join suppinv B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and A.citemno='$itm' and A.cunit='$itmunit'
	
	UNION ALL
	
	Select A.ncost as nprice, A.cunit, A.nfactor, C.nmarkup, A.dcutdate as dreceived from
	tblinvin A
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and YEAR(A.dcutdate) <= '2017' and A.citemno='$itm' and A.cunit='$itmunit'
	)A
	
	order by A.dreceived DESC LIMIT 1";
	
	$result = mysqli_query ($con, $sql);

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			echo $row['nprice'];
			
		}
	}
	else{
		echo 0;
	}


	


?>
