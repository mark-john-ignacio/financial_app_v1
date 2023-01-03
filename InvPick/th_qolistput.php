<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.*,c.citemdesc
		from so_t a 
		left join items c on a.compcode=c.compcode and a.citemno=c.cpartno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."'";

		$result = mysqli_query ($con, $sql); 
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			//if($row['nqty']>=1){
		
			 $json['nident'] = $row['nident'];
			 $json['id'] = $row['citemno'];
			 $json['cdesc'] = $row['citemdesc'];
			 $json['cunit'] = $row['cunit'];
			 $json['cmainuom'] = $row['cmainunit'];
			 $json['nfactor'] = $row['nfactor'];
			 $json['nqty'] = $row['nqty'];
			 $json['xref'] = $row['ctranno'];

		 $json2[] = $json;

//	}
	
	}


	echo json_encode($json2);


?>
