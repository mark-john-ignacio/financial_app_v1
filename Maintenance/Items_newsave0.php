<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');

$cItemNo = strtoupper($_REQUEST['txtcitemno']);
$company = $_SESSION['companyid'];
	
	$cItemDesc =  mysqli_real_escape_string($con, strtoupper($_REQUEST['txtcdesc']));
	$cUnit = $_REQUEST['seluom'];
	$cClass = $_REQUEST['selclass'];
	$cType = $_REQUEST['seltyp']; 
	$PurchCost = $_REQUEST['txtnpurchcost'];
	$RetCost = $_REQUEST['txtnretcost'];
	$Qty = $_REQUEST['txtnqty'];
	$SalesCode = $_REQUEST['txtsalesacctD'];
	$WRRCode = $_REQUEST['txtrracctD'];
	$Discount = $_REQUEST['txtndiscount'];
	$TaxRate = $_REQUEST['txtnTaxRate'];
	$Seltax = $_REQUEST['seltax'];
	
	$preparedby = $_SESSION['employeeid'];
	
	//INSERT NEW ITEM
	if (!mysqli_query($con, "INSERT INTO `items`(`compcode`, `cpartno`, `cscancode`, `citemdesc`, `cunit`, `npurchcost`, `nretailcost`, `nqty`, `ntax`, `ltaxinc`, `ndiscount`, `ctype`, `cacctcodesales`, `cacctcodewrr`, `cclass`) VALUES ('$company','$cItemNo','$cItemNo','$cItemDesc','$cUnit',$PurchCost,$RetCost,$Qty,$TaxRate,$Seltax,$Discount,'$cType','$SalesCode','$WRRCode','$cClass')"));	
	{
		printf("Errormessage: %s\n", mysqli_error($con));
	}

	$UnitRowCnt = $_REQUEST['hdnunitrowcnt'];
	//INSERT FACTOR IF MERON
	if($UnitRowCnt>=1){
		echo $UnitRowCnt;
		for($z=1; $z<=$UnitRowCnt; $z++){
			$cItemUnit = $_REQUEST['selunit'.$z];
			$cItemPurch = $_REQUEST['txtpurch'.$z];
			$cItemRetail = $_REQUEST['txtretail'.$z];
			$cItemFactor = $_REQUEST['txtfactor'.$z];
			
			//mysqli_query($con,"INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `npurchcost`, `nretailcost`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cItemPurch,$cItemRetail)");
			
			if (!mysqli_query($con, "INSERT INTO `items_factor`(`compcode`, `cpartno`, `nfactor`, `cunit`, `npurchcost`, `nretailcost`) VALUES ('$company','$cItemNo',$cItemFactor,'$cItemUnit',$cItemPurch,$cItemRetail)")) {
			printf("Errormessage: %s\n", mysqli_error($con));
			} 


			$cItemUnit = "";
			$cItemPurch = 0;
			$cItemRetail = 0;
			$cItemFactor = 0;

		}
	}

	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company', '$cItemNo','$preparedby',NOW(),'INSERTED','ITEM','$compname','Insert New Item')");

?>
<form action="Items_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtcitemno" id="txtcitemno" value="<?php echo $cItemNo;?>" />
</form>
<script>
	alert('Record Succesfully Inserted');
    document.forms['frmpos'].submit();
</script>