<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$ccvno = $_REQUEST['x'];


	
	//ewt and vat accts PURCH_VAT EWTPAY
	$disreg = array();
	$disregEWT = "";
	$sql = "Select * from accounts_default where compcode='$company' and ccode in ('PURCH_VAT','EWTPAY')";
	$result = mysqli_query ($con, $sql); 
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$disreg[] = $row['cacctno'];
		if($row['ccode']=="EWTPAY"){
			$disregEWT = $row['cacctno'];
		}
	}

	$mysql = "Select G.compcode, G.ctranno, SUM(G.ncredit) as newtamt
	From apv_t G 
	left join apv H on G.compcode=H.compcode and G.ctranno=H.ctranno
	left join accounts I on G.compcode=I.compcode and G.cacctno=I.cacctid
	Where G.compcode='$company' and G.cacctno = '".$disregEWT."' and G.ctranno in (select capvno from paybill_t where compcode='$company' and ctranno='$ccvno')
	group by G.compcode, G.ctranno";

	$result = mysqli_query ($con, $mysql); 
		
	//$json2 = array();
	//$json = [];
	$xc = 0;
	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$xc = $xc + floatval($row['newtamt']);
	
		}
	}
	
	//echo "<pre>";
	//print_r($json2);
	//echo "</pre>";

	echo $xc;


?>
