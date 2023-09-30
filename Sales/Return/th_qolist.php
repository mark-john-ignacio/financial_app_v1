<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	
	$json2 = array();

	$arrsinos = array();
	$sql = "select a.*,b.citemdesc, 1 as navail, d.ccurrencycode
		from sales_t a 
		left join items b on a.compcode=b.compcode and a.citemno=b.cpartno
		left join sales d on a.compcode=d.compcode and a.ctranno=d.ctranno
		WHERE a.compcode='$company' and d.ccode='".$_REQUEST['x']."' and d.lapproved=1 and d.lvoid=0 and d.csalestype='Goods' Order By d.ddate desc, d.ctranno desc";
		
	$result = mysqli_query ($con, $sql); 
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$nqty1 = floatval($row['nqty']);
			$nqty2 = floatval($row['nqtyreturned']); 

			if(($nqty1-$nqty2) > 0){
				$arrsinos[] = $row['ctranno'];
			}
		}
	}

	$result = mysqli_query ($con, "Select ctranno, ddate, ngross from sales where ctranno in ('".implode("','",$arrsinos)."')"); 
	if (mysqli_num_rows($result)!=0){

		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	
			 $json['cpono'] = $row['ctranno'];
			 $json['dcutdate'] = $row['ddate'];
			 $json['ngross'] = $row['ngross'];
			 $json2[] = $json;
	
		}
	}
	
	
	echo json_encode($json2);


?>
