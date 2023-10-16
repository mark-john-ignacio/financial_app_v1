<?php

	if(!isset($_SESSION)){
		session_start();
	}

	require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid']; 

	$sql = "select * from items where compcode='$company' and ctradetype='Trade' and citemdesc LIKE '%".$_GET['query']."%'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$json['id'] = $rs['cpartno'];
		$json['value'] = $rs['citemdesc'];
		$json['name'] = $rs['citemdesc'];
		$json['nprice'] = 0;
		$json['cunit'] = $rs['cunit'];
		$json['nuprice'] = 0;
		$json['ndisc'] = 0;
		$json['nbal'] = 0;
		$json2[] = $json;
	
	}


echo json_encode($json2);
//echo $sql;

?>
