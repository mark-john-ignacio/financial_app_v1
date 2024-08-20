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
	 
	$sql = "Select A.nprice
		from purchase_t A
		left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono
		where A.compcode='$company' and A.citemdesc='$itm' and A.cunit='$itmunit' and B.ccode='$custver' order by B.ddate DESC LIMIT 1";
	
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
