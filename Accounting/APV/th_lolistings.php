<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	if($_REQUEST['y']!=""){
		$qry = "and A.ctranno not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}
	
	if($_REQUEST['cust']!=""){
		$qrycust = " and A.ccode='".$_REQUEST['cust']."'";
		
		if($_REQUEST['typ']=="PettyCash"){

				$qrycust = "";
		}
		

	}else{
		$qrycust = "";
	}

	
	$result = mysqli_query ($con, "select A.*, B.cname from loans A left join customers B on A.compcode=B.compcode and A.ccode=B.cempid where A.compcode='$company' and A.lapproved=1 and A.ctranno not in (Select crefno from apv_d) ".$qry.$qrycust); 
	
	//$json2 = array();
	//$json = [];
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
	
			 $json['crrno'] = $row['ctranno'];
			 $json['ngross'] = $row['namount'];
			 $json['ddate'] = $row['ddate'];
			 $json['cremarks'] = $row['cname'];
			 $json['cacctno'] = "";
			 $json['ctitle'] = "";
			 $json2[] = $json;
	
		}
	}
	else{
			 $json['crrno'] = "NONE";
			 $json['ngross'] = "";
			 $json['ddate'] = "";
			 $json['cremarks'] = "";
			 $json['cacctno'] = "";
			  $json2[] = $json;

	}
	
	echo json_encode($json2);


?>
