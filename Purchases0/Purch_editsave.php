<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$cSINo = $_REQUEST['txtcpono'];
$company = $_SESSION['companyid'];

	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcust'];
	$dTranDate = $_REQUEST['date_delivery'];
	$dDelDate = $_REQUEST['date_needed'];
	$cRemarks = $_REQUEST['txtremarks']; 
	//$cSalesType = $_REQUEST['seltype'];
	$cSalesType = "";
	$nGross = $_REQUEST['txtnGross'];
	
	$chkCustAcct = mysqli_query($con,"select cacctcode from suppliers where compcode='$company' and  ccode='$cCustID'");

	if (!mysqli_query($con, "select cacctcode from suppliers where compcode='$company' and ccode='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
	//	if($cSalesType == "Grocery"){
			$AccntCode = $rowaccnt['cacctcode'];
	//	}
	//	elseif($cSalesType == "Cripples"){
	//		$AccntCode = $rowaccnt['cacctcodecripples'];
	//	}

	}

	$preparedby = $_SESSION['employeeid'];
	
	//UPDATE HEADER
	mysqli_query($con,"Update purchase set `ccode` ='$cCustID', `cremarks`='$cRemarks', `cpurchasetype`='$cSalesType', `dcutdate`=STR_TO_DATE('$dTranDate', '%m/%d/%Y'),`dneeded`=STR_TO_DATE('$dDelDate', '%m/%d/%Y'),`ngross`='$nGross', `ccustacctcode`='$AccntCode' Where compcode='$company' and cpono='$cSINo'");	


	// Delete previous details
	mysqli_query($con,"Delete from purchase_t Where compcode='$company' and cpono='$cSINo'");
	//if (!mysqli_query($con, "Delete from purchase_t Where compcode='$company' and cpono='$cSINo'")) {
		//printf("Errormessage: %s\n", mysqli_error($con));
	//} 

	
	//REINSERT DETAILS
	
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
			$ItmCost = $itmaccnt['npurchcost'];
			$MainUnit = $itmaccnt['cunit'];
	
		}

	mysqli_query($con,"INSERT INTO purchase_t(`compcode`, `cpono`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `ncost`, `nfactor`, `cmainunit`, `cacctcode`, `ddateneeded`) 
	values('$company','$cSINo','$z','$cItemNo','$nQty','$cUnit','$nPrice','$nAmount',$ItmCost,$nFactor,'$MainUnit','$ItmAccnt',STR_TO_DATE('$dNeed', '%m/%d/%Y'))");

	
			$ItmAccnt = "";
			$MainUnit = "";
			$ItmCost = 0;
	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cSINo','$preparedby',NOW(),'UPDATED','PURCHASE ORDER','$compname','Updated Record')");

?>
<form action="Purch_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcpono" id="txtcpono" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>