<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

		$company = $_SESSION['companyid'];
		
		$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc 
		from purchase_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join
			(Select x.creference,x.citemno,sum(x.nqty) as nqty
			 From receive_t x
			 left join receive y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.creference='".$_GET['id']."' and y.lcancelled=0
			 group by x.creference,x.citemno
			 ) c on a.cpono=c.creference and a.citemno=c.citemno
		WHERE a.compcode='$company' and a.cpono = '".$_GET['id']."' and a.citemno = '".$_GET['itm']."'";


	//echo $sql;
	$result = mysqli_query ($con, $sql) ; 

	//$json2 = array();
	//$json = [];
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
		$nqty1 = $row['nqty'];
		$nqty2 = $row['nqty2']; 
		
		 $json['nident'] = $row['nident'];
	     $json['citemno'] = $row['citemno'];
		 $json['nqty'] = $nqty1 - $nqty2;
		 $json['cunit'] = $row['cunit'];
		 $json['cmainunit'] = $row['cmainunit'];
		 $json['nfactor'] = $row['nfactor'];
		 $json['ncost'] = $row['ncost'];
		 $json['nprice'] = $row['nprice'];
		 $json['namount'] = $row['namount'];
		 $json['citemdesc'] = $row['citemdesc'];
		 $json2[] = $json;

	}


	echo json_encode($json2);


?>
