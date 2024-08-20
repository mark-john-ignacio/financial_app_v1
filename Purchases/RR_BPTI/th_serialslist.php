<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.creference,a.nrefidentity,a.citemno,c.citemdesc,a.nqty,a.cunit,a.clotsno,a.cpacklist, a.nlocation,b.cdesc as locadesc
		from receive_t_serials a
		left join mrp_locations b on a.nlocation=b.nid
		left join items c on a.compcode=c.compcode and a.citemno=c.cpartno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['id']."'";

		$result = mysqli_query ($con, $sql); 
		if(mysqli_num_rows($result) > 0 ){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			//if($row['nqty']>=1){
		
			 $json['crefno'] = $row['creference'];
			 $json['nrefidentity'] = $row['nrefidentity'];
			 $json['citemno'] = $row['citemno']; 
			 $json['citemdesc'] = $row['citemdesc']; 
			 $json['nqty'] = $row['nqty'];
			 $json['cunit'] = $row['cunit'];			 
			 $json['nlocation'] = $row['nlocation'];
			 $json['locadesc'] = $row['locadesc']; 
			 $json['cpacklist'] = $row['cpacklist']; 
			 $json['clotsno'] = $row['clotsno'];
			 
		 	$json2[] = $json;

//	}
	
		}
			echo json_encode($json2);

		}else{
			echo "";
		}


	


?>
