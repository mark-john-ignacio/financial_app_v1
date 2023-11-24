<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
		 
	$itm = $_REQUEST['itm'];
	$itmunit = $_REQUEST['cunit'];
	$custver = $_REQUEST['cust'];
	$dte = $_REQUEST['dte'];
	
//ChckItemPricing
$sqlchkprice = "Select * From items where compcode='$company' and cpartno='$itm'";
$reschkpr = mysqli_query ($con, $sqlchkprice);

	$reschkz =  mysqli_fetch_array($reschkpr, MYSQLI_ASSOC);
	
	$varpricing = $reschkz['cpricetype'];


if($varpricing=="MU"){
	$varpricing = $reschkz['nmarkup'];
	
	//get latestRR price / per smallest UOM
	$sqlA = "Select A.nprice, A.cunit, A.nfactor, A.nmarkup, A.dreceived
	From (
	Select A.nprice, C.cunit, A.nfactor, C.nmarkup, B.dreceived from
	receive_t A
	left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and B.dreceived <= '$dte' and A.citemno='$itm'
	
	UNION ALL
	
	Select A.ncost as nprice, A.cunit, A.nfactor, C.nmarkup, A.dcutdate as dreceived from
	tblinvin A
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and YEAR(A.dcutdate) <= '2017' and A.citemno='$itm'
	and A.dcutdate <= '$dte'
	)A
	
	order by A.dreceived DESC";
	
	//echo $sqlA;
	
    $resA = mysqli_query ($con, $sqlA);
	//echo mysqli_num_rows($resA);
	
	if(mysqli_num_rows($resA)!=0){
		$reschkA =  mysqli_fetch_array($resA, MYSQLI_ASSOC);
		
		$varprice = $reschkA['nprice'];
		$varnfactor = $reschkA['nfactor'];
		$varunit = $reschkA['cunit'];
		$varmarkup = $reschkA['nmarkup'];
		
		//echo $varprice.":".$varnfactor.":".$varunit.":".$varmarkup;
		
		//echo "<br>".$varunit." <> ".$itmunit;
		
		$varprice = floatval($varprice) / floatval($varnfactor); // price per smallest
		
		//$varfinprice = $reschkA['nprice'];
		
		
		//pag nde same ng unit na requested, kunin price per smallest unit multiply sa convertion factor
		if($varunit <> $itmunit){
			//kunin factor ng requested unit
			
			$sqlun = "Select * From items_factor where compcode='$company' and cpartno='$itm' and cunit='$itmunit'";
				$resun = mysqli_query ($con, $sqlun);
				$reschkun =  mysqli_fetch_array($resun, MYSQLI_ASSOC);
	
				$varfactor2 = $reschkun['nfactor'];
				
				$varfinprice = floatval($varprice) * floatval($varfactor2);
					
		}
		else{
			
			$varfinprice = $varprice;
		}
			
			echo ($varfinprice*($varmarkup/100)) + $varfinprice;
	} else{
			echo 0;
	}
}

elseif($varpricing=="PM"){	 
	$sql = "Select A.nprice from
	items_pm_t A left join items_pm B on A.compcode=B.compcode and A.ctranno=B.ctranno
	where A.compcode='$company' and B.cversion='SPC' and B.deffectdate <=  STR_TO_DATE('$dte', '%m/%d/%Y') and A.citemno='$itm' and A.cunit='$itmunit' 
	order by B.deffectdate DESC LIMIT 1";
	
	//echo $sql;
	
	$result = mysqli_query ($con, $sql);

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			echo $row['nprice'];
			
		}
	}
	else{
		echo 0;
	}

}
	


?>
