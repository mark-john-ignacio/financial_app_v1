<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['tran'];
	$typ = $_REQUEST['type'];

//GET POST DATE
$sqlpostdte = mysqli_query($con,"Select A.ddate from logfile A where A.compcode='$company' and ctranno='$tran' and cevent in ('POSTED','AUTO POST') order by ddate desc");
if (mysqli_num_rows($sqlpostdte)!=0) {
	$rowdte = mysqli_fetch_assoc($sqlpostdte);
	$dtepost = $rowdte["ddate"];
}



function getcostfromin($getcitmno, $getnqty){
	global $company;
	global $con;

	$sql = "Select A.ctranno, A.ncost, A.ntotqty, IFNULL(B.ntotout,0) as ntotout, A.nidentity from tblinvin A left join (select crefIn, sum(ntotqty) as ntotout, nrefidentity from tblinvout where compcode='$company' and citemno='$getcitmno' Group by crefIn, nrefidentity) B on A.ctranno=B.crefIn and A.nidentity=B.nrefidentity where A.compcode='$company' and A.citemno='$getcitmno' and A.ntotqty - IFNULL(B.ntotout,0) >= 1 order by A.dexpired, A.nidentity";
	
	//echo $sql."<br>";
	
	$sqlcostinvin = mysqli_query($con,$sql);
	
	if (mysqli_num_rows($sqlcostinvin)!=0) {
		$rowcostinvin = mysqli_fetch_assoc($sqlcostinvin);
		
		$qtyIN = $rowcostinvin["ntotqty"];
		$qtyOUT = $rowcostinvin["ntotout"];
		
		//echo $qtyIN." : ".$qtyOUT." : ";
		
		$qtyleft = (float)$qtyIN - (float)$qtyOUT;
		
		//echo $qtyleft."<br><br>";
		
		$array["id"] = $rowcostinvin["ctranno"];
		$array["cost"] = $rowcostinvin["ncost"];
		$array["qty"] = $qtyleft;
		$array["refident"] = $rowcostinvin["nidentity"];

	}
	else{
		$array["id"] = "NONE";
		$array["cost"] = 0;
		$array["qty"] = $getnqty;
		$array["refident"] = "";

	}
	
	return $array;
	

}

	
	//Delete muna existing if meron pra iwas double;
	mysqli_query($con,"DELETE FROM `tblinventory` where `ctranno` = '$tran'");
	mysqli_query($con,"DELETE FROM `tblinvin` where `ctranno` = '$tran'");
	mysqli_query($con,"DELETE FROM `tblinvout` where `ctranno` = '$tran'");
	 
	if($typ=="RR"){
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', '$dtepost',B.dreceived,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, A.ncost, 0, 0, 0, 0 From receive_t A left join receive b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dexpired`) Select '$company', '$tran', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, A.ncost, '$dtepost', A.dexpired From receive_t A left join receive b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.ctranno='$tran'");	
			
			if (!mysqli_query ($con, "Update `transactions` set cremarks='Y' Where ctranno='$tran'")){
				echo mysqli_error($con);
			}
		}
	}


	if($typ=="PRet"){
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', '$dtepost',B.dreturned,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, 0, 0, 0, A.nqty*A.nfactor, A.ncost, 0 From purchreturn_t A left join purchreturn b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`,`crefin`) Select '$company', '$tran', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, A.ncost, '$dtepost', B.dreturned, A.creference From purchreturn_t A left join purchreturn B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.ctranno='$tran'");	
		}
	}

	
	else if($typ=="DR"){
		
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', '$dtepost','B.dcutdate','$typ', A.citemno, A.cunit, A.nqty, A.cmainunit,A.nfactor, 0, 0, 0, A.nqty*A.nfactor, 0, A.nprice From dr_t A left join dr b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
		}
		else{
			echo "True";
			
			
			//get cost and ref in
				
			$sqldrdet = "Select A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor as totqty, B.dcutdate, A.nprice, A.namount From dr_t A left join dr b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'";
			
			$resdrdet= mysqli_query ($con, $sqldrdet); 
		
			while($row = mysqli_fetch_array($resdrdet, MYSQLI_ASSOC)){
				
				 $drtitmno = $row['citemno'];
				 //$drdsc  = $row['citemdesc'];
				 $drqty = $row['nqty'];
				 $drtotqty = $row['totqty'];
				 $drunit = $row['cunit'];
				 $drprice = $row['nprice'];
				 $dramt = $row['namount'];
				 $drmainuom = $row['cmainunit'];
				 $drfactor = $row['nfactor'];
				 
				 $dcutdate = $row['dcutdate'];
				 
				 $citemgetqty = 0;
				 $totqty = 0;
				 $totqtyremain = $drtotqty;
				 $qtyinser = 0;
				 $citemgetid= "";
				
				 do {
					 $citemget = getcostfromin($drtitmno, $drtotqty);
					 
					 $citemgetid = $citemget["id"];
					 $citemgetcost = $citemget["cost"];
					 $citemgetqty = $citemget["qty"];
					 
					 if($citemgetqty > $totqtyremain){
						//echo "SA A: ".$citemgetqty. " > " . $totqtyremain."<br>";
						$qtyinsert = $totqtyremain;
					 }
					 else{
					//	echo "SA B: ".$citemgetqty. " > " . $totqtyremain."<br>";
						$qtyinsert = $citemgetqty;
						$totqtyremain = $totqtyremain - $citemgetqty;
	
					 }
					 
					 $totqty = $totqty + $qtyinsert;				 
	
					 
					// echo "Tran No.: ".$citemgetid."<br>";
					// echo "Cost: ".$citemgetcost."<br>";
					// echo "Qty.: ".$qtyinsert."<br>";
					 
					// echo "TOTQty.: ".$citemgetqty."<br>";
					// echo "QtyRem.: ".$totqtyremain."<br>";
					
					 mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`,`crefin`) values ('$company','$tran','$drtitmno','$drunit','$drqty','$drmainuom','$drfactor','$qtyinsert','$citemgetcost', '$dtepost','$dcutdate','$citemgetid') "); 
					 
				 }while($totqty < $drtotqty);
				 
			}
	

		}
		
	}
	
	else if($typ=="SI"){
		
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', '$dtepost','B.dcutdate','$typ', A.citemno, A.cunit, A.nqty, A.cmainunit,A.nfactor, 0, 0, 0, A.nqty*A.nfactor, 0, A.nprice From sales_t A left join sales b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
		}
		else{
			echo "True";
			
			//get cost and ref in
				
			$sqldrdet = "Select A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor as totqty, B.dcutdate, A.nprice, A.namount From sales_t A left join sales b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'";
			
			$resdrdet= mysqli_query ($con, $sqldrdet); 
		
			while($row = mysqli_fetch_array($resdrdet, MYSQLI_ASSOC)){
				
				 $drtitmno = $row['citemno'];
				 //$drdsc  = $row['citemdesc'];
				 $drqty = $row['nqty'];
				 $drtotqty = $row['totqty'];
				 $drunit = $row['cunit'];
				 $drprice = $row['nprice'];
				 $dramt = $row['namount'];
				 $drmainuom = $row['cmainunit'];
				 $drfactor = $row['nfactor'];
				 
				 $dcutdate = $row['dcutdate'];
				 
				 $citemgetqty = 0;
				 $totqty = 0;
				 $totqtyremain = $drtotqty;
				 $qtyinser = 0;
				 $citemgetid= "";
				
				 do {
					 $citemget = getcostfromin($drtitmno, $drtotqty);
					 
					 $citemgetid = $citemget["id"];
					 $citemgetcost = $citemget["cost"];
					 $citemgetqty = $citemget["qty"];
					 $citemgetrefi = $citemget["refident"];
					 
					 if($citemgetqty > $totqtyremain){
						//echo "SA A: ".$citemgetqty. " > " . $totqtyremain."<br>";
						$qtyinsert = $totqtyremain;
					 }
					 else{
					//	echo "SA B: ".$citemgetqty. " > " . $totqtyremain."<br>";
						$qtyinsert = $citemgetqty;
						$totqtyremain = $totqtyremain - $citemgetqty;
	
					 }
					 
					 $totqty = $totqty + $qtyinsert;				 
	
					 
					// echo "Tran No.: ".$citemgetid."<br>";
					// echo "Cost: ".$citemgetcost."<br>";
					// echo "Qty.: ".$qtyinsert."<br>";
					 
					// echo "TOTQty.: ".$citemgetqty."<br>";
					// echo "QtyRem.: ".$totqtyremain."<br>";
					
					 mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `nrefidentity`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`,`crefin`) values ('$company','$citemgetrefi','$tran','$drtitmno','$drunit','$drqty','$drmainuom','$drfactor','$qtyinsert','$citemgetcost', '$dtepost','$dcutdate','$citemgetid') "); 
					 
				 }while($totqty < $drtotqty);

			if (!mysqli_query ($con, "Update `transactions` set cremarks='Y' Where ctranno='$tran'")){
				echo mysqli_error($con);
			}

				 
			}
	

		}
		
	}



?>
