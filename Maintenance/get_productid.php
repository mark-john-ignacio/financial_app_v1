<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


 	$company = $_SESSION['companyid'];
	$c_id = $_REQUEST['c_id'];
	
		$sql = "select  A.cpartno, A.citemdesc
		from items A 
		where A.compcode='$company' and A.cpartno = '".$c_id."' and A.cstatus='ACTIVE'";

	//echo $sql;
	$rsd = mysqli_query($con,$sql);
	if(mysqli_num_rows($rsd)>=1){
		while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		 
		 $c_prodid = $rs['cpartno'];
		 $c_prodnme = $rs['citemdesc']; 
		 		 
		}
		
		echo $c_prodid.",".$c_prodnme;
	} 
	
	else {
		echo "";
	}
	
	 
	 exit();  
 
?>
