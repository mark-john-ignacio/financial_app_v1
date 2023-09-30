<?php
session_start();
require_once "../../Connection/connection_string.php";


	$company = $_SESSION['companyid'];
	
	$sql = "Select * From aradjustment where compcode='$company' and lapproved=1 and lvoid=0 and crefsi='".$_REQUEST["x"]."' order by dcutdate, ctranno";

	$result = mysqli_query ($con, $sql);
	
	$json2 = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
		$json['ctype'] = ($row['ctype']=="Credit") ? "CM" : "DM";
		$json['refsi'] = $row['crefsi'];
		$json['reftran'] = $row['ctranno'];
		$json['dte'] = $row['dcutdate'];
		$json['grss'] = $row['ngross'];
		$json['grss'] = $row['ngross'];
		$json['rmks'] = $row['cremarks'];

		$json2[] = $json;
		 
	}

	echo json_encode($json2);

?>
