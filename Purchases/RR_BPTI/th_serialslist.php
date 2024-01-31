<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.creference,a.nrefidentity,a.citemno,a.nqty,a.cunit,a.cserial,a.cbarcode, a.nlocation,DATE_FORMAT(a.dexpired,'%m/%d/%Y') as dexpired,b.cdesc as locadesc
		from receive_t_serials a
		left join locations b on a.nlocation=b.nid
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."'";

		$result = mysqli_query ($con, $sql); 
		if(mysqli_num_rows($result) > 0 ){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			//if($row['nqty']>=1){
		
			 $json['crefno'] = $row['creference'];
			 $json['nrefidentity'] = $row['nrefidentity'];
			 $json['citemno'] = $row['citemno'];
			 $json['nqty'] = $row['nqty'];
			 $json['cunit'] = $row['cunit'];
			 $json['cserial'] = $row['cserial'];
			 $json['nlocation'] = $row['nlocation'];
			 $json['dexpired'] = $row['dexpired'];
			 $json['locadesc'] = $row['locadesc']; 
			 $json['cbarcode'] = $row['cbarcode']; 
			 
		 	$json2[] = $json;

//	}
	
		}
			echo json_encode($json2);

		}else{
			echo "";
		}


	


?>
