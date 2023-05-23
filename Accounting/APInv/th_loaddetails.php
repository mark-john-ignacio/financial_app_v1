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

		$sql = "select X.*, A.citemdesc, C.ladvancepay
		from suppinv_t X
		left join items A on X.compcode=A.compcode and X.citemno=A.cpartno
		left join purchase C on X.compcode=C.compcode and X.crefPO=C.cpono
		where X.compcode='$company' and X.ctranno = '$cpono' Order by X.nident";

	//echo $sql;
	
	$resultmain = mysqli_query ($con, $sql); 

	while($row = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){

			$json['xref'] = $row['creference'];
			$json['nident'] = $row['nrefidentity'];
			$json['xrefPO'] = $row['crefPO'];
			$json['nidentPO'] = $row['nrefidentity_po'];
			$json['ladvancepay'] = $row['ladvancepay'];
			$json['id'] = $row['citemno'];
			$json['cdesc'] = $row['citemdesc'];
			$json['nqty'] = $row['nqty'];
			$json['nqtyorig'] = $row['nqtyorig'];
			$json['cunit'] = $row['cunit'];
			$json['nprice'] = $row['nprice'];
			$json['namount'] = $row['namount'];
			$json['nbaseamount'] = $row['nbaseamount'];
			$json['cmainuom'] = $row['cmainunit'];
			$json['nfactor'] = $row['nfactor'];
			$json['cvatcode'] = $row['cvatcode'];
			$json['cewtcode'] = $row['cewtcode'];	
			// $json['dexpired'] = $row['ddateex'];
			 
			 
		
		$json2[] = $json;

	}
	

	echo json_encode($json2);


?>
