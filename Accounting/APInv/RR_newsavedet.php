<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

$company = $_SESSION['companyid'];

$sqlhead = mysqli_query($con,"select * from suppinv where compcode='$company' and ctranno = '".$_REQUEST['trancode']."'");
if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustID = $row['ccode'];
	}
}



		$cSINo = $_REQUEST['trancode'];
		//$dneed = $_REQUEST['dneed'];
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice'];
		$nAmount = $_REQUEST['namt']; 
		$nBaseAmount = $_REQUEST['nbaseamt'];
		
		$cRef = $_REQUEST['xcref'];
		$nQtyOrig = $_REQUEST['nqtyorig'];
		$nRefIdent = $_REQUEST['crefidnt'];

		$cVTCode = $_REQUEST['vatcode'];
		$nRate = $_REQUEST['nrate'];
			
		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];

		$chkItmAcct = mysqli_query($con,"select cacctcodewrr from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodewrr from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodewrr'];
	
		}
		
		if($cUnit==$cMainUOM){
			$ncost = $nPrice;
		}
		else{
			$ncost = (float)$nPrice / ((float)$nQty * (float)$nFactor);
		}

	$refcidenttran = $cSINo."P".$indexz;

	//echo "INSERT INTO suppinv_t(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `nbaseamount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cRef', '$nRefIdent', '$cItemNo', '$nQty', '$nQtyOrig', '$cUnit', '$nPrice', '$nAmount', '$nBaseAmount', $ncost, $nFactor, '$cMainUOM', '$ItmAccnt')";
	
	if (!mysqli_query($con,"INSERT INTO suppinv_t(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `nbaseamount`, `nnetvat`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`, `cvatcode`, `nrate`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cRef', '$nRefIdent', '$cItemNo', '$nQty', '$nQtyOrig', '$cUnit', '$nPrice', '$nAmount', '$nBaseAmount', '$nBaseAmount', $ncost, $nFactor, '$cMainUOM', '$ItmAccnt', '$cVTCode', '$nRate')")){
		echo "False";
		//echo "Error:".mysqli_error($con)."<br>";
	//	echo "INSERT INTO receive_t(`compcode`, `cidentity`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`) values('$company', '$refcidenttran', '$cSINo', '$indexz', '$cRef', '$nRefIdent', '$cItemNo', '$nQty', '$nQtyOrig', '$cUnit', '$nPrice', '$nAmount', $ncost, $nFactor, '$cMainUOM', '$ItmAccnt'";
	}
	else{
		echo "True";

		//Compute Tax per item if company is set as VATABLE ... 
		
		$sqlhead = mysqli_query($con,"Select A.compvat, B.lcompute from company A left join vatcode B on A.compcode=B.compcode and A.compvat=B.cvatcode where A.compcode='$company'");
		if (mysqli_num_rows($sqlhead)!=0) {
			$row = mysqli_fetch_assoc($sqlhead);
			$xvatcode = $row["compvat"];
			$xcomp = $row["lcompute"];
		}
		
		if($xcomp==1){ // pag for compute ang VAT ng mismong company check naman ung customer
			
			$sqlhead = mysqli_query($con,"Select A.cvattype, B.lcompute from suppliers A left join vatcode B on A.compcode=B.compcode and A.cvattype=B.cvatcode where A.compcode='$company' and A.ccode='$CustID'");
			if (mysqli_num_rows($sqlhead)!=0) {
				$row = mysqli_fetch_assoc($sqlhead);
				$xyvatcode = $row["cvattype"];
				$xycomp = $row["lcompute"];
			}
			
				if($xycomp == 1){
					
					if($nRate!=0){
						//echo "<br>RATE: ".(100/(int)$nrate)."<br>";
						//$rate = 
						$varnetvat = (float)$nAmount/(float)(1 + ((int)$nRate)/100);
						$varlessvat = (float)$varnetvat * (float)((int)$nRate/100);
						
						
						//echo "NETVAT: ".$nAmount." / ".(float)(1 + (100 / (int)$nrate))." = ".$varnetvat."<br>";
						//echo "LESSNETVAT: ".$varnetvat." * ".(float)(100 / (int)$nrate)." = ".$varlessvat."<br>";
						
						mysqli_query($con,"UPDATE suppinv_t set nnetvat=$varnetvat, nlessvat=$varlessvat where compcode='$company' and ctranno='$cSINo' and nident='$indexz'");
						
					}
					
				}

		}
	}

?>
