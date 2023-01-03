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


$chkSales = mysqli_query($con,"select * from receive where compcode='$company' Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "RR".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];
	}
	
	//echo $lastSI."<br>";
	//echo substr($lastSI,2,2)." <> ".$dmonth."<br>";
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "RR".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "RR".$dmonth.$dyear.$baseno;
	}
}

	
	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcust'];
	$dDate = $_REQUEST['date_delivery'];
	$dRecDate = $_REQUEST['rec_delivery'];
	$cRemarks = $_REQUEST['txtremarks']; 
	//$cSalesType = $_REQUEST['seltype'];
	
	$cSalesType = "";
	$nGross = $_REQUEST['txtnGross'];
	
	$chkCustAcct = mysqli_query($con,"select cacctcode, cacctcode from suppliers where compcode='$company' and ccode='$cCustID'");

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
	mysqli_query($con,"INSERT INTO receive(`compcode`, `ctranno`, `ccode`, `cremarks`, `creceivetype`, `ddate`, `dcutdate`, `dreceived`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`) 
	values('$company', '$cSINo', '$cCustID', '$cRemarks', '$cSalesType',NOW(), STR_TO_DATE('$dDate', '%m/%d/%Y'), STR_TO_DATE('$dRecDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode')");
	

	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	
	//echo $_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cRef = $_REQUEST['txtcreference'.$z];
		$cRefIdent = $_REQUEST['txtnrefident'.$z];
		$cItemNo = $_REQUEST['txtitemcode'.$z];
		$nQty = $_REQUEST['txtnqty'.$z];
		$nQtyOrig = $_REQUEST['txtnqtyOrig'.$z];
		$cUnit = $_REQUEST['txtcunit'.$z];
		$nPrice = $_REQUEST['txtnprice'.$z];
		$nAmount = $_REQUEST['txtnamount'.$z]; 
		$nFactor = $_REQUEST['txtnfactor'.$z];


		$chkItmAcct = mysqli_query($con,"select cacctcodewrr, npurchcost, cunit from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodewrr, npurchcost, cunit from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
			//$ItmCost = 0;
			$MainUnit = "";
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodewrr'];
			//$ItmCost = $itmaccnt['npurchcost'];
			$MainUnit = $itmaccnt['cunit'];
	
		}
		
		$ItmCost = $nPrice / $nFactor;
		//$ItmRetail = abang pa ng formula;

	mysqli_query($con,"INSERT INTO receive_t(`compcode`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `ncost`, `nretail`, `nfactor`, `cmainunit`, `cacctcode`) 
	values('$company','$cSINo','$z','$cRef','$cRefIdent','$cItemNo','$nQty','$nQtyOrig','$cUnit','$nPrice','$nAmount',$ItmCost,0,$nFactor,'$MainUnit', '$ItmAccnt')");
	
			$ItmAccnt = "";
			$MainUnit = "";
			
		//if (!mysqli_query($con, "INSERT INTO salesreturn_t(`compcode`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `ncost`, `cacctcode`) 
	//values('$company','$cSINo','$z','$cRef','$cRefIdent','$cItemNo','$nQty','$cUnit','$nPrice','$nAmount','$ItmCost','$ItmAccnt')")) {
			//printf("Errormessage: %s\n", mysqli_error($con));
		//} 

	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','RECEIVING','$compname','Inserted New Record')");

?>
<form action="Received_edit.php" name="frmSR" id="frmSR" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Saved');
    document.forms['frmSR'].submit();
</script>