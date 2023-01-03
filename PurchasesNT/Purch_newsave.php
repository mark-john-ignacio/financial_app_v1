<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$dmonth = date("m");
//$dmonth = "01";
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from purchase where compcode='$company' Order By cpono desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "PO".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['cpono'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "PO".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "PO".$dmonth.$dyear.$baseno;
	}
}

	
	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcust'];
	$dTranDate = $_REQUEST['date_delivery'];
	$dDelDate = $_REQUEST['date_needed'];
	$cRemarks = $_REQUEST['txtremarks']; 
	//$cSalesType = $_REQUEST['seltype'];
	$cSalesType = "";
	$nGross = $_REQUEST['txtnGross'];
	
	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
		//if($cSalesType == "Grocery"){
			$AccntCode = $rowaccnt['cacctcode'];
		//}
		//elseif($cSalesType == "Cripples"){
		//	$AccntCode = $rowaccnt['cacctcodecripples'];
		//}

	}

	$preparedby = $_SESSION['employeeid'];
	
	//INSERT HEADER	
	mysqli_query($con,"INSERT INTO purchase(`compcode`, `cpono`, `ccode`, `cremarks`, `cpurchasetype`, `ddate`, `dcutdate`, `dneeded`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`) 
	values('$company', '$cSINo', '$cCustID', '$cRemarks', '$cSalesType', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode')");
	

	//if (!mysqli_query($con, "INSERT INTO purchase(`compcode`, `cpono`, `ccode`, `cremarks`, `cpurchasetype`, `ddate`, `dcutdate`, `dneeded`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`) 
	//values('$company', '$cSINo', '$cCustID', '$cRemarks', '$cSalesType', NOW(), STR_TO_DATE('$dTranDate', '%m/%d/%Y'), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode')")) {
		//printf("Errormessage: %s\n", mysqli_error($con));
	//} 
	
	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	
	//echo $_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cItemNo = $_REQUEST['txtitemcode'.$z];
		$nQty = $_REQUEST['txtnqty'.$z];
		$cUnit = $_REQUEST['txtcunit'.$z];
		$nFactor = $_REQUEST['hdnfactor'.$z];
		$nPrice = $_REQUEST['txtnprice'.$z];
		$nAmount = $_REQUEST['txtnamount'.$z];
		$dNeed = $_REQUEST['dneed'.$z];


		$chkItmAcct = mysqli_query($con,"select cacctcodesales, npurchcost, cunit from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodesales, npurchcost, cunit from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
			$MainUnit = "";
			$ItmCost = 0;
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodesales'];
			$MainUnit = $itmaccnt['cunit'];
			$ItmCost = $itmaccnt['npurchcost'];
	
		}
		
		//$ItmCost = $nPrice / $nFactor;

	mysqli_query($con,"INSERT INTO purchase_t(`compcode`, `cpono`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`, `ddateneeded`) 
	values('$company','$cSINo','$z','$cItemNo','$nQty','$cUnit','$nPrice','$nAmount',$ItmCost,$nFactor,'$MainUnit','$ItmAccnt',STR_TO_DATE('$dNeed', '%m/%d/%Y'))");
	
			$ItmAccnt = "";
			$MainUnit = "";
			$ItmCost = 0;

	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','PURCHASE ORDER','$compname','Inserted New Record')");

?>
<form action="Purch_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcpono" id="txtcpono" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmpos'].submit();
</script>