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
	$cSalesType = $_REQUEST['seltype'];
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
	mysqli_query($con,"Update purchreturn set `ccode`='$cCustID', `cremarks`='$cRemarks', `creturntype`='$cSalesType', `dcutdate`=STR_TO_DATE('$dDate', '%m/%d/%Y'), `dreturned`=STR_TO_DATE('$dRecDate', '%m/%d/%Y'), `ngross`='$nGross', `ccustacctcode`='$AccntCode' Where compcode='$company' and ctranno='$cRRNo'");
	
	// Delete previous details
	mysqli_query($con,"Delete from purchreturn_t Where compcode='$company' and ctranno='$cRRNo'");
	if (!mysqli_query($con, "Delete from purchreturn_t Where compcode='$company' and ctranno='$cRRNo'")) {
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
		
		$nCost = $_REQUEST['txtncost'.$z]; 
		$nRetail = $_REQUEST['txtnretail'.$z]; 
		


		$chkItmAcct = mysqli_query($con,"select cacctcodesales, npurchcost, cunit from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodesales, npurchcost, cunit from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
			//$ItmCost = 0;
			$MainUnit = "";
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodesales'];
			//$ItmCost = $itmaccnt['npurchcost'];
			$MainUnit = $itmaccnt['cunit'];
	
		}
		
		//$ItmCost = $nPrice / $nFactor;

	mysqli_query($con,"INSERT INTO purchreturn_t(`compcode`, `ctranno`, `nident`, `creference`, `nrefidentity`, `citemno`, `nqty`, `nqtyorig`, `cunit`, `nprice`, `namount`, `ncost`, `nretail`, `nfactor`, `cmainunit`, `cacctcode`) 
	values('$company','$cRRNo','$z','$cRef','$cRefIdent','$cItemNo',$nQty,$nQtyOrig,'$cUnit',$nPrice,$nAmount,$nCost,$nRetail,$nFactor,'$MainUnit', '$ItmAccnt')");
	
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
	values('$company','$cRRNo','$preparedby',NOW(),'UPDATED','PURCH RETURN','$compname','Updated Record')");

?>
<form action="PurchRet_edit.php" name="frmSR" id="frmSR" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $cRRNo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmSR'].submit();
</script>