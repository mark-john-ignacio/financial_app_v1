<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$date1 = date("Y-m-d");

	if($_REQUEST['y']!=""){
		$qry = "and a.nident not in ('". str_replace(",","','",$_REQUEST['y']) . "')";
	}
	else{
		$qry = "";
	}
/*
		$sql = "select a.*,ifnull(c.nqty,0) as nqty2,b.citemdesc, 1 as navail, d.ccurrencycode
		from sales_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join sales d on a.compcode=d.compcode and a.ctranno=d.ctranno
		left join
			(
			 Select x.creference,x.citemno,x.nrefident,sum(x.nqty) as nqty
			 From salesreturn_t x
			 left join salesreturn y on x.compcode=y.compcode and x.ctranno=y.ctranno
			 Where x.creference='".$_REQUEST['x']."' and y.lcancelled=0
			 group by x.creference,x.citemno,x.nrefident
			) c on a.ctranno=c.creference and a.citemno=c.citemno and a.nident = c.nrefident

		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry." Order By a.nident";

		*/

		$sql = "select a.*,b.citemdesc, 1 as navail, d.ccurrencycode
		from sales_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join sales d on a.compcode=d.compcode and a.ctranno=d.ctranno
		WHERE a.compcode='$company' and a.ctranno = '".$_REQUEST['x']."' ".$qry." Order By a.nident";

	//echo $sql;
	
	$result = mysqli_query ($con, $sql); 

	//$json2 = array();
	//$json = [];
// echo	mysqli_num_rows($result);
	if (mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		
			$nqty1 = $row['nqty'];
			$nqty2 = $row['nqtyreturned']; 
		
			 $json['ident'] = $row['nident'];
			 $json['citemno'] = $row['citemno'];
			 $json['cdesc'] = $row['citemdesc'];
			 $json['cunit'] = $row['cunit'];
			 $json['nqty'] = $nqty1 - $nqty2;
			 $json['navail'] = $row['navail'];
			 $json['nprice'] = $row['nprice'];
			 $json['nbaseamount'] = $row['nbaseamount'];
			 $json['ccurrencycode'] = $row['ccurrencycode'];
			 $json2[] = $json;
	
		}

	}
	else{
			$json['citemno'] = "";
			$json['cdesc'] = "";
			$json['cunit'] = "";
			$json['nqty'] = "";
			$json['navail'] = "";
			$json['nprice'] = "";
			$json['nbaseamount'] = "";
			$json['ccurrencycode'] = "";
			$json2[] = $json;
	}
	
	echo json_encode($json2);

?>
