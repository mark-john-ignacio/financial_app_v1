<?php
if(!isset($_SESSION)){
	session_start();
	}
require_once "../Connection/connection_string.php";
//$q = strtolower($_GET["q"]);

//if (!$q) return;

	//$sql = "select * from items where cpartno LIKE '%$q%'";

	//$rsd = mysqli_query($con,$sql);
	//if (!mysqli_query($con, $sql)) {
	//  printf("Errormessage: %s\n", mysqli_error($con));
	//} 
	
	//while($rs = mysqli_fetch_array($rsd, MYSQLI_ASSOC)) {
	//	$cid = $rs['cpartno'];
	//	$cname = $rs['citemdesc'];
	//	$nprice = $rs['namount'];
	//	echo "$cid|$cname|$nprice\n";
	//}

//echo $sql;

 $c_id = $_REQUEST['c_id'];
 $result = mysqli_query($con,"SELECT * FROM items WHERE cpartno = '$c_id'"); 
 $all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);

 
 	$thefinprice = 0;
 	$varpricing = $all_course_data['cpricetype'];

    $itmunit = $all_course_data['cunit'];
	$custver = "PM1";
	$dte = date("Y-m-d");

	$company = $_SESSION['companyid'];

 if($varpricing=="MU"){
	$varpricing = $all_course_data['nmarkup'];
	
	//get latestRR price / per smallest UOM
	$sqlA = "Select A.nprice, A.cunit, A.nfactor, A.nmarkup, A.dreceived
	From (
	Select A.nprice, C.cunit, A.nfactor, C.nmarkup, B.dreceived from
	receive_t A
	left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and B.lapproved=1 and B.dreceived <= '$dte' and A.citemno='$c_id'
	
	UNION ALL
	
	Select A.ncost as nprice, A.cunit, A.nfactor, C.nmarkup, A.dcutdate as dreceived from
	tblinvin A
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and YEAR(A.dcutdate) <= '2017' and A.citemno='$c_id'
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
			
			$sqlun = "Select * From items_factor where compcode='$company' and cpartno='$c_id' and cunit='$itmunit'";
				$resun = mysqli_query ($con, $sqlun);
				$reschkun =  mysqli_fetch_array($resun, MYSQLI_ASSOC);
	
				$varfactor2 = $reschkun['nfactor'];
				
				$varfinprice = floatval($varprice) * floatval($varfactor2);
					
		}
		else{
			
			$varfinprice = $varprice;
		}
			
			//echo ($varfinprice*($varmarkup/100)) + $varfinprice;

			$thefinprice = ($varfinprice*($varmarkup/100)) + $varfinprice;

	} else{
		//	echo 0;

		$thefinprice = 0;
	}
}

elseif($varpricing=="PM"){	 
	$sql = "Select A.nprice from
	items_pm_t A left join items_pm B on A.compcode=B.compcode and A.ctranno=B.ctranno
	where A.compcode='$company' and B.cversion='".$custver."' and B.deffectdate <=  '$dte' and A.citemno='$c_id' and A.cunit='$itmunit' and B.lapproved = 1
	order by B.deffectdate DESC LIMIT 1";
	
	//echo $sql;
	
	$result = mysqli_query ($con, $sql);

	if(mysqli_num_rows($result)!=0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			$thefinprice = $row['nprice'];
			
		}
	}
	else{
		$thefinprice = 0;
	}

}

elseif($varpricing=="MUFIX"){
	$varpricing = $reschkz['nmarkup'];
	
	//get latestRR price / per smallest UOM
	$sqlA = "Select A.nprice, A.cunit, A.nfactor, A.nmarkup, A.dreceived
	From (
	Select A.nprice, C.cunit, A.nfactor, C.nmarkup, B.dreceived from
	receive_t A
	left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and B.lapproved=1 and B.dreceived <= '$dte' and A.citemno='$c_id'
	
	UNION ALL
	
	Select A.ncost as nprice, A.cunit, A.nfactor, C.nmarkup, A.dcutdate as dreceived from
	tblinvin A
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and A.citemno='$itm'
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
			
			$sqlun = "Select * From items_factor where compcode='$company' and cpartno='$c_id' and cunit='$itmunit'";
				$resun = mysqli_query ($con, $sqlun);
				$reschkun =  mysqli_fetch_array($resun, MYSQLI_ASSOC);
	
				$varfactor2 = $reschkun['nfactor'];
				
				$varfinprice = floatval($varprice) * floatval($varfactor2);
					
		}
		else{
			
			$varfinprice = $varprice;
		}
			
		$thefinprice = $varmarkup + $varfinprice;
			
	} else{
		$thefinprice = 0;
	}
}
 
 $c_prodid = $all_course_data['cpartno'];
 $c_prodnme = $all_course_data['citemdesc']; 
 $c_price = $thefinprice; 
 $c_unit = $all_course_data['cunit']; 
 //$c_discount = $all_course_data['ndiscount'];
 $c_discount = 0;
 echo $c_prodid.",".$c_prodnme.",".$c_price.",".$c_unit.",".$c_discount;
 exit();  
 
?>
