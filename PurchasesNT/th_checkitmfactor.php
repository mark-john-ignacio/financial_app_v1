<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
		 
	$itm = $_REQUEST['itm'];
	$itmunit = $_REQUEST['cunit'];	
	 
	$sql = "Select A.nfactor from items_factor A where A.compcode='$company' and A.cpartno='$itm' and A.cunit='$itmunit' and A.cstatus='ACTIVE'";
	
	$result = mysqli_query ($con, $sql);

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			echo $row['nfactor'];
			
		}
	}
	else{
		echo 1;
	}


	


?>
