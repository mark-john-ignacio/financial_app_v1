<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$cpono = $_REQUEST['id'];
	
		//$sql = "select X.*, A.citemdesc, DATE_FORMAT(X.dexpired,'%m/%d/%Y') as ddateex
		//from receive_t X
		//left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		//where X.compcode='$company' and X.ctranno = '$cpono' Order by X.nident";

		$sql = "select X.*
		from receive_t X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		where X.compcode='$company' and X.ctranno = '$cpono' Order by X.nident";

	//echo $sql;
	
	$resultmain = mysqli_query ($con, $sql); 

	while($row = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

		$json['xref'] = $row['creference'];
		$json['nident'] = $row['nrefidentity'];
		$json['id'] = $row['citemno'];
		$json['cdesc'] = $row['citemdesc'];
		$json['cskucode'] = $row['cskucode'];
		$json['nqty'] = $row['nqty'];
		$json['nqtyorig'] = $row['nqtyorig'];
		$json['cunit'] = $row['cunit'];
		$json['nprice'] = 0;
		$json['nbaseamount'] = 0;
		$json['namount'] = 0;
		$json['cmainuom'] = $row['cmainunit'];
		$json['nfactor'] = $row['nfactor'];		
		$json['ncostid'] = $row['ncostcenterid'];
		$json['ncostdesc'] = ($row['ncostcenterdesc']==null) ? "" : $row['ncostcenterdesc'];	 
		$json['cremarks'] = ($row['cremarks']==null) ? "" : $row['cremarks'];

		$json2[] = $json;

	}
	

	echo json_encode($json2);


?>
