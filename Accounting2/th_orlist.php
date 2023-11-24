<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if ($_POST['y'] <> "") {
		$salesno = str_replace(",","','",$_POST['y']);
		
		$qry = " and csalesno not in ('".$salesno."') ";
	}
	else {
		$qry = " ";
	}
	
	$result = mysqli_query ($con, "select * from sales where compcode='$company' and lapproved=1 and ccode='".$_POST['x']."'".$qry."order by dcutdate desc, csalesno desc"); 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){

	     $json['csalesno'] = $row['csalesno'];
		 $json['dcutdate'] = $row['dcutdate'];
		 $json['ngross'] = $row['ngross'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
