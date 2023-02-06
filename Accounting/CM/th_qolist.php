<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$ccode = $_REQUEST['x'];

	//get all current returns
	$result = mysqli_query ($con, "select a.creference, a.nrefident, sum(a.nqty) as nqty from aradj_t a left join aradj b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lcancelled=0 and b.ccode='".$ccode."'"); 
	$arradj = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arradj[] = $row;
	}

	//print_r($arradj);
	//echo "<br><br>";

	$resInvoices = mysqli_query ($con, "select b.ccode, a.ctranno, a.nident, a.nqty from sales_t a left join sales b on a.compcode=b.compcode and a.ctranno=b.ctranno where a.compcode='$company' and b.lapproved=1 and b.ccode='".$ccode."'"); 
	$arrsales = array();
	while($row2 = mysqli_fetch_array($resInvoices, MYSQLI_ASSOC)){
		$arrsales[] = $row2;
	}

	//print_r($arrsales);
	//echo "<br><br>";

//check all remaining qty
$transarray = array();
	foreach($arrsales as $rx){

		$remqty = 1;
		foreach($arradj as $xy){
			if($rx['ctranno']==$xy['creference'] && $rx['nident']==$xy['nrefident']){
				if(floatval($rx['nqty'])<=$xy['nqty']){
					$remqty = 0;
				}
				break;
			}
		}

		if($remqty==1){
			$transarray[] = $rx['ctranno'];
		}	

	}

	//print_r($transarray);
	//echo "<br><br>";

	$result = mysqli_query ($con, "select ccode,ctranno,ngross,dcutdate from sales where compcode='$company' and ctranno in ('".implode("','", $transarray)."')"); 
	$arrfintrans = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arrfintrans[] = $row;
	}


	$json = array();
	foreach($arrfintrans as $row){
	
			$json['ccode'] = $row['ccode'];
			$json['cpono'] = $row['ctranno'];
			$json['dcutdate'] = $row['dcutdate'];
			$json['ngross'] = $row['ngross'];
			$json2[] = $json;
	
	}

	if(count($json)==0){
		$json['cpono'] = "NONE";
		$json2[] = $json;
	}
	
	
	echo json_encode($json2);


?>
