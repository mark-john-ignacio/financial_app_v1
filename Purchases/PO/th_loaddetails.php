<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$cpono = $_REQUEST['id'];
	
		$sql = "select X.citemno as cpartno, A.citemdesc as citemdesc1, X.citemdesc as citemdesc2, X.cunit, X.nqty, X.nprice, X.nbaseamount, X.namount, X.cmainunit, X.nfactor, X.ddateneeded, ifnull(X.cremarks,'') as cremarks, X.cewtcode, X.ctaxcode
		from purchase_t X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		where X.compcode='$company' and X.cpono = '$cpono' order by nident";

	//echo $sql;
	
	$resultmain = mysqli_query ($con, $sql); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

		$json['id'] = $row2['cpartno'];
		if($row2['cpartno']=="NEW_ITEM"){
			$json['desc'] = $row2['citemdesc2'];
		}else{
			$json['desc'] = $row2['citemdesc1'];
		}
		$json['cunit'] = strtoupper($row2['cunit']);
		$json['nqty'] = $row2['nqty'];
		$json['nprice'] = $row2['nprice'];
		$json['nbaseamount'] = $row2['nbaseamount'];
		$json['namount'] = $row2['namount'];
		$json['cmainunit'] = strtoupper($row2['cmainunit']);
		$json['nfactor'] = $row2['nfactor'];
		$json['dneed'] = $row2['ddateneeded'];
		$json['cremarks'] = $row2['cremarks'];
		$json['cewtcode'] = $row2['cewtcode'];
		$json['ctaxcode'] = $row2['ctaxcode'];
		$json2[] = $json;

	}
	

	echo json_encode($json2);


?>
