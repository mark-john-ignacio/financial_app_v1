<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$qryx = "";
	
	if($_REQUEST['uomzx']!=""){
		$uomzx = str_replace(",","','",$_REQUEST['uomzx']); 
		$qryx = " Where A.cunit not in ('".$uomzx."')";
	}
		
	$qry = "Select A.* From
			(
			SELECT A.cunit, B.cDesc, 1 as nFactor FROM items A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$_REQUEST['id']."'
			UNION ALL
			SELECT A.cunit, B.cDesc, A.nfactor as nFactor FROM items_factor A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$_REQUEST['id']."' AND A.cstatus='ACTIVE'
			) A ". $qryx;

	
	$rs = mysqli_query ($con, $qry); 
	
	
	
	if (mysqli_num_rows($rs) != 0)
	{
		$getRow = mysqli_fetch_assoc($rs);
		echo $getRow['cunit'];
		
	} else {
		echo "0";
	}
	

	

?>
