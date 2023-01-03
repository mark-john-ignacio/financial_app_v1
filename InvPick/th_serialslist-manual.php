<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.ctranno, a.cidentity,a.citemno,a.nqty,a.nfactor,a.cunit,a.cserial,a.nlocation,DATE_FORMAT(a.dexpired,'%m/%d/%Y') as dexpired,b.cdesc as locadesc, ifnull(c.nqty,0) as soqty, ifnull(c.nfactor,0) sofactor
		from receive_putaway_t a
		left join receive_putaway_location b on a.compcode=b.compcode and a.nlocation=b.nid
		left join so_pick_t c on a.compcode=c.compcode and a.ctranno=c.crefput and a.cidentity = c.crefputident
		left join receive_putaway d on a.compcode=d.compcode and a.ctranno=d.ctranno
		WHERE a.compcode='$company' and a.citemno = '".$_REQUEST['itm']."' 
		Group By a.nrefidentity,a.citemno,a.nqty,a.nfactor,a.cunit,a.cserial,a.nlocation,a.dexpired,b.cdesc
		HAVING SUM(a.nqty*a.nfactor) - SUM(ifnull(c.nqty,0)*ifnull(c.nfactor,0)) > 0 Order By a.dexpired ";

		$result = mysqli_query ($con, $sql); 
		$rowcntr = 0;
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$rowcntr++;
		
			//set ilan nlng natitira
			 
			 $remainqty = ((float)$row['nqty']*(float)$row['nfactor']) - ((float)$row['soqty']*(float)$row['sofactor']);		 
			 
			 $json['ctranno'] = $row['ctranno'];
			 $json['nrefidentity'] = $row['cidentity'];
			 $json['citemno'] = $row['citemno'];
			 $json['nqty'] = $remainqty;
			 $json['cunit'] = $_REQUEST['mainuom'];
			 $json['cserial'] = $row['cserial'];
			 $json['nlocation'] = $row['nlocation'];
			 $json['dexpired'] = $row['dexpired'];
			 $json['locadesc'] = $row['locadesc'];

		 	 $json2[] = $json;
	}
	

	if($rowcntr>0){

		echo json_encode($json2);

	}else{
		echo "{}";
	}


?>
