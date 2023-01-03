<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$apikey = $_SESSION['currapikey'];;

	$from_currency = $_REQUEST['fromcurr'];
	$to_currency = $_REQUEST['tocurr'];
	$amount = 1;

	$from_Currency = urlencode($from_currency);
	$to_Currency = urlencode($to_currency);

	//$query =  "{$from_Currency}_{$to_Currency}";

	// change to the free URL if you're using the free version
	//$json = @file_get_contents("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apikey}");
	$json = file_get_contents("https://api.currencyfreaks.com/latest?apikey=4c151e86299e4588939cdbb45a606021");

		//if ( $json === false )
	//	{
	//		echo number_format(1, 2, '.', '');
	//	}else{
	//		$obj = json_decode($json, true);

	//		$val = floatval($obj["$query"]);
		
		
		//	$total = $val * $amount;
	//		echo number_format($total, 2, '.', '');
	//	}


	$objcurrs = $json;
  $objrows = json_decode($json, true);

	$xval = 0;
	$baseval = 0;

	foreach($objrows['rates'] as $key => $val){
		if($key==$to_Currency){
			$xval = $val;
		}

		if($key==$from_Currency){
			$baseval = $val;
		}
	}

	if($xval!==0 && $baseval!==0){

		$total = floatval($xval) / floatval($baseval);
		echo number_format($total, 2, '.', '');

	}else{
		echo 1.00;
	}


	
	

	//echo json_encode($json2);


?>
