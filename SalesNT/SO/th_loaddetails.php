<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$csalesno = $_REQUEST['id'];
	$avail = $_REQUEST['itmbal'];

	$date1 = date("Y-m-d");
	
	if($avail==1){
		$sql = "select X.nrefident, X.creference as ctranno, X.citemno as cpartno, A.citemdesc, X.cunit, X.nqty as totqty, 1 as nqty, X.nprice, X.nbaseamount, X.namount, A.cunit as qtyunit, X.nfactor
		from ntso_t X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		where X.compcode='$company' and X.ctranno = '$csalesno'";
	}
	else{
		$sql = "select X.nrefident, X.creference as ctranno, X.citemno as cpartno, A.citemdesc, X.cunit, X.nqty as totqty, X.nprice, X.nbaseamount, X.namount, A.cunit as qtyunit, X.nfactor,(TRIM(TRAILING '.' FROM(CAST(TRIM(TRAILING '0' FROM B.nqty)AS char)))) AS nqty
		from ntso_t X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno 
		left join 
			(
				select COALESCE((Sum(nqtyin*nfactor)-sum(nqtyout*nfactor)),0) as nqty, a.cunit, a.citemno, a.nfactor
				From tblinventory a
				where a.compcode='$company' and a.dcutdate <= '$date1'
				Group by a.cunit, a.citemno
			) B on X.citemno=B.citemno
		where X.compcode='$company' and X.ctranno = '$csalesno'";
	}

	//echo $sql;
	
	$resultmain = mysqli_query ($con, $sql); 

	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

		$json['id'] = $row2['cpartno'];
		$json['desc'] = $row2['citemdesc'];
		$json['cunit'] = strtoupper($row2['cunit']);
		$json['nqty'] = $row2['nqty'];
		$json['totqty'] = $row2['totqty'];
		$json['nprice'] = $row2['nprice'];
		$json['namount'] = $row2['namount']; 
		$json['nbaseamount'] = $row2['nbaseamount'];
		$json['cqtyunit'] = strtoupper($row2['qtyunit']);
		$json['nfactor'] = $row2['nfactor'];
		$json['xref'] = $row2['ctranno'];
		$json['nident'] = $row2['nrefident'];
		
		$json2[] = $json;

	}
	

	echo json_encode($json2);


?>
