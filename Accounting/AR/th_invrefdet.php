<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

		$sql = "select a.*,b.citemdesc
		from sales_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' Order By a.nidentity";

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	//$json2 = array();
	//$json = [];
// echo	mysqli_num_rows($result);
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				
			 $json['csi'] = $row['creference'];
			 $json['citemno'] = $row['citemno'];
			 $json['cdesc'] = $row['citemdesc'];
			 $json['cunit'] = $row['cunit'];
			 $json['nqty'] = $row['nqty'];
			 $json['nprice'] = $row['nprice'];
			 $json['namount'] = $row['namount'];
			 $json2[] = $json;
	
		}

	}
	else{
		    $json['csi'] = "";
			$json['citemno'] = "";
			$json['cdesc'] = "";
			$json['cunit'] = "";
			$json['nqty'] = "";
			$json['nprice'] = "";
			$json['namount'] = "";
			$json2[] = $json;
	}
	
	echo json_encode($json2);

?>
