<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$dmonth = date("m");
	$dyear = date("y");
	$company = $_SESSION['companyid'];

	$chkSales = mysqli_query($con,"select * from sales where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ddate desc LIMIT 1");
	if (mysqli_num_rows($chkSales)==0) {
		$cSINo = "SI".$dmonth.$dyear."00000";
	}
	else {
		while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
			$lastSI = $row['ctranno'];
		}
		
		if(substr($lastSI,2,2) <> $dmonth){
			$cSINo = "SI".$dmonth.$dyear."00000";
		}
		else{
			$baseno = intval(substr($lastSI,6,5)) + 1;
			$zeros = 5 - strlen($baseno);
			$zeroadd = "";
			
			for($x = 1; $x <= $zeros; $x++){
				$zeroadd = $zeroadd."0";
			}
			
			$baseno = $zeroadd.$baseno;
			$cSINo = "SI".$dmonth.$dyear.$baseno;
		}
	}
	
	$cCustID = $_REQUEST['ccustid'];
	$cCustName = $_REQUEST['ccustname'];
	$dDelDate = date("m/d/Y");
	$cRemarks = "NULL"; 
	$nGross = $_REQUEST['GrandTot']; 
	
	$nDue = $_REQUEST['nnet'];
	$nPayed = $_REQUEST['GrandPayed']; 
	$nChanged = $_REQUEST['totchange'];
	$nDisc = $_REQUEST['ndisc'];

		$chkCustAcct = mysqli_query($con,"select cacctcodesales, cvattype from customers where compcode='$company' and cempid='$cCustID'");
	
		if (!mysqli_query($con, "select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
						
		while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
			
				$AccntCode = $rowaccnt['cacctcodesales'];
				$cVatCode = $rowaccnt['cvattype'];
	
		}

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER
	$sqlinsert = "INSERT INTO sales(`compcode`, `ctranno`, `ccode`, `cremarks`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `cacctcode`, `cvatcode`) 
	values('$company', '$cSINo', '$cCustID', $cRemarks, NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 1, 0, '$AccntCode', '$cVatCode')";
	
	if (!mysqli_query($con, $sqlinsert)) {
		echo  "False";
		echo  mysqli_error($con);
	} 
	else{
		echo $cSINo;
		
	
		if (!mysqli_query($con,"INSERT INTO sales_pos(`compcode`, `ctranno`, `ntotal`, `ndiscount`, `nnettotal`, `npayed`, `nchanged`) 
	values('$company', '$cSINo', $nGross, $nDisc, $nDue, $nPayed, $nChanged)")) {
		//echo  "False";
		//	echo  mysqli_error($con);
		} 


	}

?>
