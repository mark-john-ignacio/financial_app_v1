<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$result = mysqli_query ($con, "SELECT cacctno, cacctdesc FROM accounts WHERE compcode='$company' and (cacctdesc like '%".$_REQUEST['query']."%' OR cacctno like '%".$_REQUEST['query']."%') and ctype='Details' and ccategory='".$_REQUEST['ccat']."' and mainacct='".$_REQUEST['cmain']."' and cacctno<>'".$_REQUEST['cid']."'"); 

	 
	 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['id'] = $row['cacctno'];
		 $json['name'] = $row['cacctdesc'];
		 $json2[] = $json;

	}

	echo json_encode($json2);


?>
