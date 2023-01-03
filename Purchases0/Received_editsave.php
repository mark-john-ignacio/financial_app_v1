<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$cRRNo = $_REQUEST['txtctranno'];
$company = $_SESSION['companyid'];

	
	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcust'];
	$dDate = $_REQUEST['date_delivery'];
	$dRecDate = $_REQUEST['rec_delivery'];
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
	
	//UPDATE HEADER
	mysqli_query($con,"Update receive set `ccode`='$cCustID', `cremarks`='$cRemarks', `creceivetype`='$cSalesType', `dcutdate`=STR_TO_DATE('$dDate', '%m/%d/%Y'), `dreceived`=STR_TO_DATE('$dRecDate', '%m/%d/%Y'), `ngross`='$nGross', `ccustacctcode`='$AccntCode' Where compcode='$company' and ctranno='$cRRNo'");
	
	// Delete previous details
	mysqli_query($con,"Delete from receive_t Where compcode='$company' and ctranno='$cRRNo'");
	if (!mysqli_query($con, "Delete from receive_t Where compcode='$company' and ctranno='$cRRNo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


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
			$MainUnit = "";
			//$ItmCost = 0;
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodewrr'];
			//$ItmCost = $itmaccnt['npurchcost'];
			$MainUnit = $itmaccnt['cunit'];
	
		}

		$ItmCost = $nPrice / $nFactor;
		//$ItmRetail = abang pa ng formula;
		
	mysqli_query($con,"INSERT INTO receive_t(`compcode`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `ncost`, `nretail`, `nfactor`, `cmainunit`, `cacctcode`) 
	values('$company','$cRRNo','$z','$cRef','$cRefIdent','$cItemNo','$nQty','$nQtyOrig','$cUnit','$nPrice','$nAmount',$ItmCost,0,$nFactor,'$MainUnit','$ItmAccnt')");
	
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
	values('$company','$cRRNo','$preparedby',NOW(),'UPDATED','RECEIVING','$compname','Updated Record')");

?>
<form action="Received_edit.php" name="frmSR" id="frmSR" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cRRNo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmSR'].submit();
</script>