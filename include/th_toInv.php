<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$tran = $_REQUEST['tran'];
	$typ = $_REQUEST['type'];



function getcostfromin($getcitmno, $getnqty){
	global $company;
	global $con;

	$sql = "Select A.ctranno, A.ncost, A.ntotqty, IFNULL(B.ntotout,0) as ntotout from tblinvin A left join (select crefIn, sum(ntotqty) as ntotout from tblinvout where compcode='$company' and citemno='$getcitmno' Group by crefIn) B on A.ctranno=B.crefIn where A.compcode='$company' and A.citemno='$getcitmno' and A.ntotqty - IFNULL(B.ntotout,0) >= 1 order by A.dexpired";
	
	//echo $sql;
	
	$sqlcostinvin = mysqli_query($con,$sql);
	
	if (mysqli_num_rows($sqlcostinvin)!=0) {
		$rowcostinvin = mysqli_fetch_assoc($sqlcostinvin);
		
		$qtyIN = $rowcostinvin["ntotqty"];
		$qtyOUT = $rowcostinvin["ntotout"];
		
		$qtyleft = (float)$qtyIN - (float)$qtyOUT;
		
		$array["id"] = $rowcostinvin["ctranno"];
		$array["cost"] = $rowcostinvin["ncost"];
		$array["qty"] = $qtyleft;
		
		return $array;
	}

}

	
	//Delete muna existing if meron pra iwas double;
	mysqli_query($con,"DELETE FROM `tblinventory` where `ctranno` = '$tran'");
	mysqli_query($con,"DELETE FROM `tblinvin` where `ctranno` = '$tran'");
	mysqli_query($con,"DELETE FROM `tblinvout` where `ctranno` = '$tran'");
	 

	if($typ=="INVCNT"){
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),B.ddatetime,'$typ', A.citemno, A.cunit, A.nqty, A.cunit, 1, A.nqty, A.nunitcost, 0, 0, 0, 0 From invcount_t A left join invcount b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `cserial`,`cbarcode`,`nlocation`,`nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dexpired`) Select '$company', '$tran', A.citemno, A.cunit, A.cserial, A.cbarcode, A.nlocation, A.nqty, A.cunit, 1, A.nqty, A.nunitcost, NOW(), A.dexpdte From invcount_t A left join invcount b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.ctranno='$tran'");	
		}
	}

	if($typ=="RR"){
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),B.dreceived,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, A.ncost, 0, 0, 0, 0 From receive_t A left join receive b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvin`(`compcode`, `ctranno`, `citemno`, `cunit`, `cserial`,`cbarcode`,`nlocation`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dexpired`) Select '$company', '$tran', A.citemno, A.cunit, A.cserial, A.cbarcode, A.nlocation, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, B.ncost, NOW(), A.dexpired From receive_t_serials A left join receive_t B on A.compcode=B.compcode and A.ctranno=B.ctranno and A.citemno=B.citemno and  A.nrefidentity=B.nident where A.ctranno='$tran'");	
		}
	}


	if($typ=="PRet"){
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),B.dreturned,'$typ', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, 0, 0, 0, A.nqty*A.nfactor, A.ncost, 0 From purchreturn_t A left join purchreturn b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
			echo mysqli_error($con);
		}
		else{
			echo "True";
			
			mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `cserial`, `cbarcode`, `nlocation`, `ddate`, `dcutdate`,`crefin`) Select '$company', '$tran', A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor, B.ncost, A.cserial, A.cbarcode, A.nlocation, NOW(), A.dexpired, B.creference From purchreturn_t_serials A left join purchreturn_t B on A.compcode=B.compcode and A.ctranno=B.ctranno and A.citemno=B.citemno and  A.nrefidentity=B.nident where A.ctranno='$tran'");	
		}
	}

	
	else if($typ=="DR"){
		
		if (!mysqli_query($con,"INSERT INTO `tblinventory`(`compcode`, `ctranno`, `ddatetime`, `dcutdate`, `ctype`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `nqtyin`, `ncostin`, `nretailin`, `nqtyout`, `ncostout`, `nretailout`) Select '$company', '$tran', NOW(),'B.dcutdate','$typ', A.citemno, A.cunit, A.nqty, A.cmainunit,A.nfactor, 0, 0, 0, A.nqty*A.nfactor, 0, A.nprice From dr_t A left join dr b on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and A.ctranno='$tran'")){
			echo "False";
		}
		else{
			echo "True";
			
			
			//get cost and ref in
				
			$sqldrdet = "Select A.citemno, A.cunit, A.nqty, A.cmainunit, A.nfactor, A.nqty*A.nfactor as totqty, A.dexpired, A.cserial, A.cbarcode, A.nlocation From dr_t_serials A where A.compcode='$company' and A.ctranno='$tran'";
			
			$resdrdet= mysqli_query ($con, $sqldrdet); 
		
			while($row = mysqli_fetch_array($resdrdet, MYSQLI_ASSOC)){
				
				 $drtitmno = $row['citemno'];
				 //$drdsc  = $row['citemdesc'];
				 $drqty = $row['nqty'];
				 $drtotqty = $row['totqty'];
				 $drunit = $row['cunit'];
				// $drprice = $row['nprice'];
				// $dramt = $row['namount'];
				 $drmainuom = $row['cmainunit'];
				 $drfactor = $row['nfactor'];
				 
				 $dcutdate = $row['dexpired'];
				 $cxserial = $row['cserial'];
				 $cxbarcode = $row['cbarcode'];
				 $cnlocationid = $row['nlocation'];
				 
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
					
					 mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `cserial`, `cbarcode`, `nlocation`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`,`crefin`) values ('$company','$tran','$drtitmno','$drunit','$drqty','$drmainuom','$cxserial','$cxbarcode','$cnlocationid','$drfactor','$qtyinsert','$citemgetcost', NOW(),'$dcutdate','$citemgetid') "); 
					 
				 }while($totqty < $drtotqty);
				 
			}
	

		}
		
	}

?>
