<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
		 
	$date1 = date("Y-m-d");
	$itm = $_REQUEST['id'];
	
	$sql = "select COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, a.cunit
	From tblinventory a
	right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	where a.compcode='$company' and  a.dcutdate <= '$date1' and a.citemno = '".$itm."' Group by a.cunit";
	
	$result = mysqli_query ($con, $sql);

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			echo $row['nqty'] ." ". strtoupper($row['cunit']);
			
		}
	}
	else{
		echo 0;
	}


	


?>
