<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.ctranno, a.nident,a.citemno,a.nqty,a.nfactor,a.cunit,a.cserial,a.nlocation,DATE_FORMAT(a.dexpired,'%m/%d/%Y') as dexpired,b.cdesc as locadesc, c.ctranno as refdr
		from receive_t_serials a
		left join locations b on a.compcode=b.compcode and a.nlocation=b.nid
		left join dr_t_serials c on a.compcode=b.compcode and a.cserial=c.cserial
		left join purchreturn_t_serials d on a.compcode=d.compcode and a.cserial=d.cserial
		WHERE a.compcode='$company' and a.citemno = '".$_REQUEST['itm']."' and a.ctranno = '".$_REQUEST['itmxref']."' and ifnull(c.ctranno,'') = ''	and ifnull(d.ctranno,'') = ''		
		Order By a.dexpired";

		$result = mysqli_query ($con, $sql); 
		$rowcntr = 0;
		if (mysqli_num_rows($result)!=0) {
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$rowcntr++;
			
				//set ilan nlng natitira		 
				 
				 $json['ctranno'] = $row['ctranno'];
				 $json['nrefidentity'] = $row['nident'];
				 $json['citemno'] = $row['citemno'];
				 $json['nqty'] = $row['nqty'];
				 $json['cunit'] = $row['cunit'];
				 $json['cserial'] = $row['cserial'];
				 $json['nlocation'] = $row['nlocation'];
				 $json['dexpired'] = $row['dexpired'];
				 $json['locadesc'] = $row['locadesc'];

			 	 $json2[] = $json;
			}
		}
	

	if($rowcntr>0){

		echo json_encode($json2);

	}else{
		echo "{}";
	}


?>
