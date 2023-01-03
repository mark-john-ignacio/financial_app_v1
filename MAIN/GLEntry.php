<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');

$cSINo = $_REQUEST['txtcsalesno'];
$company = $_SESSION['companyid'];

	
	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcust'];
	$dDelDate = $_REQUEST['date_delivery'];
	$cRemarks = $_REQUEST['txtremarks']; 
	$cSalesType = $_REQUEST['seltype'];
	$nGross = $_REQUEST['txtnGross'];
	
	$chkCustAcct = mysqli_query($con,"select cacctcodegrocery, cacctcodecripples from customers where compcode='$company' and  cempid='$cCustID'");

	if (!mysqli_query($con, "select cacctcodegrocery, cacctcodecripples from customers where compcode='$company' and cempid='$cCustID'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
		if($cSalesType == "Grocery"){
			$AccntCode = $rowaccnt['cacctcodegrocery'];
		}
		elseif($cSalesType == "Cripples"){
			$AccntCode = $rowaccnt['cacctcodecripples'];
		}

	}

	$preparedby = $_SESSION['employeeid'];
	
	//UPDATE HEADER
	mysqli_query($con,"Update sales set `ccode` ='$cCustID', `cremarks`='$cRemarks', `csalestype`='$cSalesType', `dcutdate`='$dDelDate', `ngross`='$nGross', `ccustacctcode`='$AccntCode' Where compcode='$company' and csalesno='$cSINo'");	


	// Delete previous details
	mysqli_query($con,"Delete from sales_t Where compcode='$company' and csalesno='$cSINo'");
	if (!mysqli_query($con, "Delete from sales_t Where compcode='$company' and csalesno='$cSINo'")) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	
	//REINSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	
	//echo $_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cItemNo = $_REQUEST['txtitemcode'.$z];
		$nQty = $_REQUEST['txtnqty'.$z];
		$cUnit = $_REQUEST['txtcunit'.$z];
		$nPrice = $_REQUEST['txtnprice'.$z];
		$nAmount = $_REQUEST['txtnamount'.$z];


		$chkItmAcct = mysqli_query($con,"select cacctcodesales, ncost from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodesales, ncost from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
			$ItmCost = 0;
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodesales'];
			$ItmCost = $itmaccnt['ncost'];
	
		}

	mysqli_query($con,"INSERT INTO sales_t(`compcode`, `csalesno`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `ncost`, `cacctcode`) 
	values('$company', '$cSINo','$z','$cItemNo','$nQty','$cUnit','$nPrice','$nAmount','$ItmCost','$ItmAccnt')"); 

			$ItmAccnt = "";
			$ItmCost = 0;

	}
	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `cmachine`, `cremarks`) 
	values('$company', '$cSINo','$preparedby',NOW(),'UPDATED','$compname','Updated Record')");

?>
<form action="POS_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcsalesno" id="txtcsalesno" value="<?php echo $cSINo;?>" />
</form>
<script>
	alert('Record Succesfully Updated');
    document.forms['frmpos'].submit();
</script>