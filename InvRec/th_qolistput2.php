<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		$date1 = date("Y-m-d");
		
		$sql = "select a.*,c.citemdesc,ifnull(b.nqty,0) as nqtyfin
		from receive_t a 
		left join 
				(Select x.citemno, x.nrefidentity, sum(x.nqty) as nqty 
				from receive_putaway_t x  
				Where x.compcode='$company' and x.ctranno = '".$_REQUEST['id']."'
				Group by x.citemno, x.nrefidentity				
				) as b on a.citemno=b.citemno and a.nident=b.nrefidentity
		left join items c on a.compcode=c.compcode and a.citemno=c.cpartno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['ref']."'";

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
			 $json['nqtyfin'] = $row['nqtyfin'];

		 $json2[] = $json;

//	}
	
	}


	echo json_encode($json2);


?>
