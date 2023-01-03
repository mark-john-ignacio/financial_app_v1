<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	$qryx = "";
	
	//if($_REQUEST['uomzx']!=""){
	//	$uomzx = str_replace(",","','",$_REQUEST['uomzx']); 
	//	$qryx = "AND A.cunit not in ('".$uomzx."')";
	//}
	
	$resultmain = mysqli_query ($con, "SELECT A.cunit, B.cDesc, 1 as nFactor FROM items A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$_REQUEST['id']."' ".$qryx); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

	     $json['id'] = $row2['cunit'];
		 $json['name'] = $row2['cDesc'];
		 $json['fact'] = "Main Unit";
		 $json2[] = $json;

	}
	
	$result = mysqli_query ($con, "SELECT A.cunit, B.cDesc, A.nfactor as nFactor FROM items_factor A left join groupings B on A.compcode=B.compcode and A.cunit=B.ccode WHERE A.compcode='$company' and A.cpartno = '".$_REQUEST['id']."' AND A.cstatus='ACTIVE' ".$qryx); 

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['cunit'];
		 $json['name'] = $row['cDesc'];
		 $json['fact'] = $row['nFactor'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
