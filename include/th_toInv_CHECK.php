<?php
if(!isset($_SESSION)){
session_start();
}
require_once "../Connection/connection_string.php";

$company = $_SESSION['companyid'];
$tran = $_REQUEST['tran'];

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
					echo "SA A: ".$citemgetqty. " > " . $totqtyremain."<br>";
					$qtyinsert = $totqtyremain;
				 }
				 else{
					echo "SA B: ".$citemgetqty. " > " . $totqtyremain."<br>";
					$qtyinsert = $citemgetqty;
					$totqtyremain = $totqtyremain - $citemgetqty;

				 }
				 
				 $totqty = $totqty + $qtyinsert;				 

				 
				 echo "Tran No.: ".$citemgetid."<br>";
				 echo "Cost: ".$citemgetcost."<br>";
				 echo "Qty.: ".$qtyinsert."<br>";
				 
				 echo "TOTQty.: ".$citemgetqty."<br>";
				 echo "QtyRem.: ".$totqtyremain."<br>";
				
				 mysqli_query($con,"INSERT INTO `tblinvout`(`compcode`, `ctranno`, `citemno`, `cunit`, `nqty`, `cmainunit`, `nfactor`, `ntotqty`, `ncost`, `ddate`, `dcutdate`,`crefin`) values ('$company','$tran','$drtitmno','$drunit','$drqty','$drmainuom','$drfactor','$qtyinsert','$citemgetcost', NOW(),'$dcutdate','$citemgetid') "); 
				 
			 }while($totqty < $drtotqty);
			 
		}
				 
?>
