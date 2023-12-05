<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

function chkgrp($valz) {
	if($valz==''){
		return "NULL";
	}else{
    	return "'".$valz."'";
	}
}


	$company = $_SESSION['companyid'];
	$CustID = $_REQUEST['ccode'];
	
		$cSINo = $_REQUEST['trancode'];
		$crefno = chkgrp($_REQUEST['crefno']); 
		$crefident = chkgrp($_REQUEST['crefident']); 
		$indexz = $_REQUEST['indx'];
		$cItemNo = $_REQUEST['citmno'];
		$nQty = $_REQUEST['nqty'];
		$cUnit = $_REQUEST['cuom'];
		$nPrice = $_REQUEST['nprice']; 
		$nDiscount = $_REQUEST['ndiscount'];
		$nAmount = $_REQUEST['namt'];
		$nTranAmount = $_REQUEST['ntranamt'];


		if(isset($_REQUEST['ewtcode']) && $_REQUEST['ewtcode']!=""){
			if(is_array($_REQUEST['ewtcode'])){
				$cewtcode = implode(",",$_REQUEST['ewtcode']);
			}else{
				$cewtcode = $_REQUEST['ewtcode'];
			}
		}else{
			$cewtcode = "";
		}

		$cewtrate = $_REQUEST["ewtrate"];
		$ctaxcode = $_REQUEST["vatcode"]; 
		$nrate = $_REQUEST["nrate"];

		$cMainUOM = $_REQUEST['mainunit'];
		$nFactor = $_REQUEST['nfactor'];

		if(isset($_REQUEST['acctid'])){
			$cacctcode = $_REQUEST['acctid']; 
		}else{
			$cacctcode = "''";
		}

		$refcidenttran = $cSINo."P".$indexz;

	//echo "INSERT INTO sales_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nrefident`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `ndiscount`, `nbaseamount`, `namount`, `cmainunit`,`nfactor`,`cacctcode`,`ctaxcode`, `nrate`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$cSINo', $crefno, $crefident, '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nDiscount', '$nTranAmount', '$nAmount', '$cMainUOM', '$nFactor', $cacctcode, '$ctaxcode', '$nrate', '$cewtcode', '$cewtrate')";

	if (!mysqli_query($con,"INSERT INTO sales_t(`compcode`, `cidentity`, `ctranno`, `creference`, `nrefident`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `ndiscount`, `nbaseamount`, `namount`, `cmainunit`,`nfactor`,`cacctcode`,`ctaxcode`, `nrate`, `cewtcode`, `newtrate`) values('$company', '$refcidenttran', '$cSINo', $crefno, $crefident, '$indexz', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nDiscount', '$nTranAmount', '$nAmount', '$cMainUOM', '$nFactor', $cacctcode, '$ctaxcode', '$nrate', '$cewtcode', '$cewtrate')")){
		//echo "False";
		
		echo "Errormessage: %s\n", mysqli_error($con);
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
					
					$sqlhead = mysqli_query($con,"Select A.cvattype, B.lcompute from customers A left join vatcode B on A.compcode=B.compcode and A.cvattype=B.cvatcode where A.compcode='$company' and A.cempid='$CustID'");
					if (mysqli_num_rows($sqlhead)!=0) {
						$row = mysqli_fetch_assoc($sqlhead);
						$xyvatcode = $row["cvattype"];
						$xycomp = $row["lcompute"];
					}
					
						if($xycomp == 1){
							
							if($nrate!=0){
								//echo "<br>RATE: ".(100/(int)$nrate)."<br>";
								//$rate = 
								$varnetvat = (float)$nAmount/(float)(1 + ((int)$nrate)/100);
								$varlessvat = (float)$varnetvat * (float)((int)$nrate/100);
								
								
								//echo "NETVAT: ".$nAmount." / ".(float)(1 + (100 / (int)$nrate))." = ".$varnetvat."<br>";
								//echo "LESSNETVAT: ".$varnetvat." * ".(float)(100 / (int)$nrate)." = ".$varlessvat."<br>";
								
								mysqli_query($con,"UPDATE sales_t set nnetvat=$varnetvat, nlessvat=$varlessvat where compcode='$company' and ctranno='$cSINo' and nident='$indexz'");
								
							}
							
						}

				}

	}
	


?>
