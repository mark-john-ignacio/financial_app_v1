<?php

	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');

	$company = $_SESSION['companyid'];

	$citemno = $_REQUEST['x'];
	$cdesc = $_REQUEST['ver'];

	$cver = "";

	$sql = mysqli_query($con,"select * from mrp_bom_label where compcode='$company' and citemno='".$citemno."'");
	$row = $sql->fetch_all(MYSQLI_ASSOC);
	foreach($row as $rs4){
		$cver = $rs4['nversion'];
	}

	$cver = intval($cver) + 1;

	if (!mysqli_query($con, "insert into mrp_bom_label(compcode,citemno,nversion,cdesc) values('".$_SESSION['companyid']."','$citemno',$cver,'$cdesc')")) {
		
		echo "False";

	}
	else{
		echo "True";
	}



?>	

