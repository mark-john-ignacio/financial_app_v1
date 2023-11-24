<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['id'];
	$dte = $_REQUEST['dte'];

//GETTING COST
	$sql = "Select A.dcutdate, A.ctranno, A.ncost, A.ntotqty, IFNULL(B.ntotout,0) as ntotout, A.nidentity from tblinvin A left join ( select crefIn, sum(ntotqty) as ntotout, nrefidentity from tblinvout where compcode='$company' and citemno='$tran' and dcutdate <= STR_TO_DATE('$dte', '%m/%d/%Y') Group by crefIn, nrefidentity) B on A.ctranno=B.crefIn and A.nidentity=B.nrefidentity where A.compcode='$company' and A.citemno='$tran' and A.ntotqty - IFNULL(B.ntotout,0) >= 1 and A.dcutdate <= STR_TO_DATE('$dte', '%m/%d/%Y') order by A.dexpired, A.nidentity";
	
	//echo $sql;
	
	$sqlcost = mysqli_query($con,$sql);
	$grandcost = 0;
	if (mysqli_num_rows($sqlcost)!=0) {
		$totqty = 0;
		$totcost = 0;
		$qtyCost = 0;
		
		while($row = mysqli_fetch_array($sqlcost, MYSQLI_ASSOC)){
		
			$qtyIN = $row["ntotqty"];
			$qtyOUT = $row["ntotout"];
			$qtyCost = $row["ncost"];
			
			//echo $qtyIN." : ".$qtyOUT." : ";
			
			$totqty = (float)$qtyIN - (float)$qtyOUT;
			$totcost = (float)$totqty * (float)$qtyCost;
			
			$grandcost = (float)$grandcost + (float)$totcost;
			
			$totqty = 0;
			$totcost = 0;
			$qtyCost = 0;

			
		}

	}


//GETTING RETAIL PRICE
$sqlchkpricing = mysqli_query($con,"Select cpricetype, nmarkup from items where compcode='$company' and cpartno='$tran'");
	if (mysqli_num_rows($sqlchkpricing)!=0) {
		$rowcostinvin = mysqli_fetch_assoc($sqlchkpricing);
		
		$grandret = 0;
		
		if($rowcostinvin["cpricetype"]=="MU"){
			
			$sqlret = mysqli_query($con,"Select A.dcutdate, A.ctranno, A.ncost, A.ntotqty, IFNULL(B.ntotout,0) as ntotout, A.nidentity from tblinvin A left join ( select crefIn, sum(ntotqty) as ntotout, nrefidentity from tblinvout where compcode='$company' and citemno='$tran' and dcutdate <= STR_TO_DATE('$dte', '%m/%d/%Y') Group by crefIn, nrefidentity) B on A.ctranno=B.crefIn and A.nidentity=B.nrefidentity where A.compcode='$company' and A.citemno='$tran' and A.ntotqty - IFNULL(B.ntotout,0) >= 1 and A.dcutdate <= STR_TO_DATE('$dte', '%m/%d/%Y') order by A.dexpired, A.nidentity");
			
			if (mysqli_num_rows($sqlret)!=0) {
				$totqty = 0;
				$totcost = 0;
				$qtyCost = 0;
				
				while($row2 = mysqli_fetch_array($sqlret, MYSQLI_ASSOC)){
				
					$qtyIN = $row2["ntotqty"];
					$qtyOUT = $row2["ntotout"];
					$qtyCost = $row2["ncost"];
					$qtyMU = $rowcostinvin["nmarkup"];
					
					//echo $qtyIN." : ".$qtyOUT." : ".$qtyCost." : ".$qtyMU;
					
					$totqty = (float)$qtyIN - (float)$qtyOUT;
					$totcost = (float)$totqty * ((float)$qtyCost + ((float)$qtyCost*((float)$qtyMU/100)));
					
					$grandret = (float)$grandret + (float)$totcost;
					
					$totqty = 0;
					$totcost = 0;
					$qtyCost = 0;
					$qtyCost = 0;
		
					
				}
		
			}
			
		}

	}
	
	echo number_format($grandcost,4).":".number_format($grandret,4);
?>
