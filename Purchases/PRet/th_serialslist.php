<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.creference,a.nrefidentity,a.citemno,a.nqty,a.cunit, a.clotsno, a.cpacklist,a.nlocation, b.cdesc as locadesc
		from purchreturn_t_serials a
		left join mrp_locations b on a.compcode=b.compcode and a.nlocation=b.nid
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."'";

		$result = mysqli_query ($con, $sql); 
		$rowcntr = 0;
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$rowcntr++;

			$json['crefno'] = $row['creference'];
			$json['nrefidentity'] = $row['nrefidentity'];
			$json['citemno'] = $row['citemno'];
			$json['nqty'] = $row['nqty'];
			$json['cunit'] = $row['cunit'];
			$json['clotsno'] = $row['clotsno'];
			$json['cpacklist'] = $row['cpacklist'];
			$json['nlocation'] = $row['nlocation'];
			$json['locadesc'] = $row['locadesc'];

			$json2[] = $json;

//	}
	
	}


	if($rowcntr>0){

		echo json_encode($json2);

	}else{
		echo "{}";
	}


?>
