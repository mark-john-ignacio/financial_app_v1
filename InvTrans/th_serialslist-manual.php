<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.ctranno, a.nident,a.citemno,a.nqty,a.nfactor,a.cunit,a.cserial,a.nlocation,DATE_FORMAT(a.dexpired,'%m/%d/%Y') as dexpired,b.cdesc as locadesc, c.ctranno as refdr
		from receive_t_serials a
		left join receive_putaway_location b on a.compcode=b.compcode and a.nlocation=b.nid
		left join (Select x0.compcode, x1.citemno, x1.cserial, x1.ctranno from dr_t_serials x1 left join dr x0 on x1.compcode=x0.compcode and x1.ctranno=x0.ctranno Where x1.compcode='$company' and x0.lcancelled=0) c on a.compcode=c.compcode and a.citemno=c.citemno and a.cserial=c.cserial
		left join (Select y0.compcode, y1.citemno, y1.cserial, y1.ctranno from purchreturn_t_serials y1 left join purchreturn y0 on y1.compcode=y0.compcode and y1.ctranno=y0.ctranno Where y1.compcode='$company' and y0.lcancelled=0) d on a.compcode=d.compcode and a.citemno=d.citemno and a.cserial=d.cserial
		WHERE a.compcode='$company' and a.citemno = '".$_REQUEST['itm']."' and ifnull(c.ctranno,'') = '' and ifnull(d.ctranno,'') = ''		
		Order By a.dexpired";
		echo $sql;
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
