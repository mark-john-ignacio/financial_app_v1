<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	
	$company = $_SESSION['companyid'];
	
	$sql = "select A.*, B.cunit as pounit, B.nfactor from items A left join items_factor B on A.compcode=B.compcode and A.cpartno=B.cpartno where A.compcode='$company' and A.citemdesc LIKE '%".$_GET['query']."%'";

	$rsd = mysqli_query($con,$sql);
	while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
		$json['id'] = $rs['cpartno'];
		$json['value'] = $rs['citemdesc'];
		$json['nfactor'] = $rs['nfactor'];
		$json['nprice'] = 0;
		$json['cunit'] = $rs['pounit'];
		
		
				if(is_null($json['nfactor'])){
					$json['nfactor'] = 1;
					$json['cunit'] = $rs['cunit'];
				}
		$json['npricefin'] = 0 * $json['nfactor'];

		$json2[] = $json;
	
	}


echo json_encode($json2);
//echo $sql;

?>
