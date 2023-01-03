<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$csalesno = $_REQUEST['id'];

	$date1 = date("Y-m-d");
	
		$sql = "select X.creference as ctranno, X.nrefident, X.citemno as cpartno, A.citemdesc, X.cunit, X.nqty as totqty, 1 as nqty, X.nprice,  X.nbaseamount, X.namount, A.cunit as qtyunit, X.nfactor, X.ndiscount, A.ctype, A.ctaxcode
		from sales_t X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		where X.compcode='$company' and X.ctranno = '$csalesno' Order By X.nident";

	//echo $sql;
	
	$resultmain = mysqli_query ($con, $sql); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

		$json['id'] = $row2['cpartno'];
		$json['desc'] = $row2['citemdesc'];
		$json['cunit'] = strtoupper($row2['cunit']);
		$json['nqty'] = $row2['nqty'];
		$json['totqty'] = $row2['totqty'];
		$json['nprice'] = $row2['nprice'];
		$json['ndisc'] = $row2['ndiscount'];
		$json['nbaseamount'] = $row2['nbaseamount'];
		$json['namount'] = $row2['namount'];
		$json['cqtyunit'] = strtoupper($row2['qtyunit']);
		$json['nfactor'] = $row2['nfactor'];
		$json['xref'] = $row2['ctranno'];
		$json['nrefident'] = $row2['nrefident'];
		$json['citmtyp'] = $row2['ctype'];
		$json['ctaxcode'] = $row2['ctaxcode'];

		$json2[] = $json;

	}
	

	echo json_encode($json2);


?>
