<?php
if(!isset($_SESSION)){
session_start();
}
include('../Connection/connection_string.php');
include('../include/denied.php');
include('../Accounting/InsertToGL.php');
include('../Accounting/InsertToInv.php');

$dmonth = date("m");
$dyear = date("y");
$company = $_SESSION['companyid'];


$chkSales = mysqli_query($con,"select * from sales where compcode='$company' and YEAR(ddate) = YEAR(CURDATE()) Order By ctranno desc LIMIT 1");
if (mysqli_num_rows($chkSales)==0) {
	$cSINo = "SI".$dmonth.$dyear."00000";
}
else {
	while($row = mysqli_fetch_array($chkSales, MYSQLI_ASSOC)){
		$lastSI = $row['ctranno'];

		
	}
	
	//echo $lastSI;
	//echo "<br>Sub: ".substr($lastSI,2,2)." <> ".$dmonth;
	if(substr($lastSI,2,2) <> $dmonth){
		$cSINo = "SI".$dmonth.$dyear."00000";
	}
	else{
		$baseno = intval(substr($lastSI,6,5)) + 1;
		$zeros = 5 - strlen($baseno);
		$zeroadd = "";
		//echo "base: ".$baseno."<br>";
		for($x = 1; $x <= $zeros; $x++){
			$zeroadd = $zeroadd."0";
		}
		
		$baseno = $zeroadd.$baseno;
		$cSINo = "SI".$dmonth.$dyear.$baseno;
	}
}

//echo "<br>SI: ".$cSINo;
	
	$cCustID = $_REQUEST['txtcustid'];
	$cCustName = $_REQUEST['txtcustname'];
	$dDelDate = $_REQUEST['date_delivery'];
	$nLimit = $_REQUEST['txtncredit']; 
	$nLimitBal = $_REQUEST['txtncreditbal']; 
	$nGross = $_REQUEST['txtnGross'];
	$nDue = $_REQUEST['txtnDue'];
	$nPayed = $_REQUEST['txtnPayed'];
	$cSalesType = $_REQUEST['seltype'];
	
	$chkCustAcct = mysqli_query($con,"select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'");

	if (!mysqli_query($con, "select cacctcodesales from customers where compcode='$company' and cempid='$cCustID'")) {
		printf("Errormessage1: %s\n", mysqli_error($con));
	} 
					
	while($rowaccnt = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
	//	if($cSalesType == "Grocery"){
	//		$AccntCode = $rowaccnt['cacctcodegrocery'];
	//	}
	//	elseif($cSalesType == "Cripples"){
	//		$AccntCode = $rowaccnt['cacctcodecripples'];
//
	//	}

		$AccntCode = $rowaccnt['cacctcodesales'];

	}

	$preparedby = $_SESSION['employeeid'];
	
	//echo "ales(`compcode`, `ctranno`, `ccode`, `csalestype`, `ncreditlimit`, `ncreditbal`, `ddate`, `dcutdate`, `ngross`, `ndue`, `npayed`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `ccustacctcode`, `cremarks`) 
	//values('$company', '$cSINo', '$cCustID', '$cSalesType', '$nLimit', '$nLimitBal', NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$nDue', '$nPayed', '$preparedby', 0, 0, 0, '$AccntCode','')";
	
	//INSERT HEADER
	if (!mysqli_query($con,"INSERT INTO sales(`compcode`, `ctranno`, `ccode`, `csalestype`, `ddate`, `dcutdate`, `ngross`, `cpreparedby`, `lcancelled`, `lapproved`, `lprintposted`, `cacctcode`, `cremarks`) 
	values('$company', '$cSINo', '$cCustID', '$cSalesType', NOW(), STR_TO_DATE('$dDelDate', '%m/%d/%Y'), '$nGross', '$preparedby', 0, 0, 0, '$AccntCode','')")) {
		printf("Errormessage2: %s\n", mysqli_error($con));
	}else{
		mysqli_query($con,"INSERT INTO sales_t_dues(`compcode`, `ctranno`, `ncreditbal`, `ndue`, `npayed`) values('$company', '$cSINo', '$nLimitBal', '$nDue', '$nPayed')");
	}
	

	//INSERT DETAILS
	
	$rowcnt = $_REQUEST['hdnrowcnt'];
	
	//echo "no: ".$_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cItemNo = $_REQUEST['txtitemcode'.$z];
		$nQty = $_REQUEST['txtnqty'.$z];
		$cUnit = $_REQUEST['txtcunit'.$z];
		$nPrice = $_REQUEST['txtnprice'.$z];
		$nAmount = $_REQUEST['txtnamount'.$z];
		
		$cMainUOM = $_REQUEST['hdnmainuom'.$z];
		$nFactor = $_REQUEST['hdnfactor'.$z];


		$chkItmAcct = mysqli_query($con,"select cacctcodesales from items where compcode='$company' and cpartno='$cItemNo'");
	
		if (!mysqli_query($con, "select cacctcodesales from items where compcode='$company' and cpartno='$cItemNo'")) {
			printf("Errormessage3: %s\n", mysqli_error($con));
		} 

			$ItmAccnt = "";
			$ItmCost = 0;
						
		while($itmaccnt = mysqli_fetch_array($chkItmAcct, MYSQLI_ASSOC)){
			
			$ItmAccnt = $itmaccnt['cacctcodesales'];
			//$ItmCost = $itmaccnt['npurchcost'];
			$ItmCost = 0;
	
		}
		$cIdentix = $cSINo.$z;

				if (!mysqli_query($con,"INSERT INTO sales_t(`compcode`, `ctranno`, `cidentity`, `nident`, `citemno`, `nqty`, `cunit`, `nprice`, `namount`, `cacctcode`, `cmainunit`, `nfactor`) values('$company', '$cSINo', '$cIdentix', '$z', '$cItemNo', '$nQty', '$cUnit', '$nPrice', '$nAmount', '$ItmAccnt', '$cMainUOM', $nFactor)")) {
					printf("Errormessage4: %s\n", mysqli_error($con)); 
				}
	
			$ItmAccnt = "";
			$ItmCost = 0;

	}

	//insert info table
	$rowcnt2 = $_REQUEST['hdnrowcnt2'];
	
	for($z2=1; $z2<=$rowcnt2; $z2++){
		$cinfocode = $_REQUEST['txtinfocode'.$z2];
		$cinfodesc = $_REQUEST['txtinfodesc'.$z2];
		$cinfofld = $_REQUEST['txtinfofld'.$z2];
		$cinfovlue = $_REQUEST['txtinfoval'.$z2];

		mysqli_query($con,"INSERT INTO sales_t_info(`compcode`, `ctranno`, `nident`, `citemno`, `cfldnme`, `cvalue`) values('$company', '$cSINo', '$z2', '$cinfocode', '$cinfofld', '$cinfovlue')");

	}

	
	//INSERT LOGFILE
	$compname = php_uname('n');
	
	mysqli_query($con,"INSERT INTO logfile(`compcode`, `ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
	values('$company','$cSINo','$preparedby',NOW(),'INSERTED','POS RETAIL','$compname','Inserted New Record')");

?>
<html>
<head>
	<title>Coop Financials</title>
      <script type="text/javascript" src="../js/jquery.js"></script>
<style>
td{ font-size:11px; font-family:Tahoma, Geneva, sans-serif }
.td2 { font-size:14px; font-family:Tahoma, Geneva, sans-serif }
.td1 { font-size:14px; font-family:Tahoma, Geneva, sans-serif; font-weight:bold }

.contz {
	margin:0px;
	width: 50%;
	max-width: 1366px;
	min-width: 780px;
 /*  background: blue; /*For browsers that do not support gradients */
/*   background: -webkit-linear-gradient(top left, #06C, #0CF); /* For Safari 5.1 to 6.0 */
 /*  background: -o-linear-gradient(right bottom, #06C, #0CF); /* For Opera 11.1 to 12.0 */
/*   background: -moz-linear-gradient(right bottom, #06C, #0CF); /* For Firefox 3.6 to 15 */
/*   background: linear-gradient(top right bottom, #06C, #0CF); /* Standard syntax */	
	overflow: hidden;
	height:90%;

}
.sidebar1 {
	float: left;
	width: 30%;
	margin: 0px;
					padding: 10px;
					border: 2px solid #06F;
					height:350px;
					text-align: left;
					overflow: auto; border-radius: 10px;
}
.content {
	float: right;
	width: 63%;
	margin: 0px;
					padding: 10px;
					border: 2px solid #06F;
					text-align: left;
					height:350px;
					overflow: auto; border-radius: 10px;
					text-align:center
	}
table{ border-collapse:collapse}
#lblSum{
	font-family:Tahoma, Geneva, sans-serif;
	font-weight:bold;

	text-align:center; }
	
.btn{
color:#fff;background-color:#428bca;border-color:#357ebd;
display:inline-block;padding:6px 12px;margin-bottom:0;font-size:14px;font-weight:400;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;cursor:pointer;background-image:none;border:1px solid transparent;border-radius:4px; width:100%;
}

}
</style>
<body>
<center>
<div class="contz">
<div class="sidebar1">
<table width="100%" border="0" align="center">
  <tr>
    <td width="15%"><b>Qty</b></td>
    <td><b>Description</b></td>
    <td align="right"><b>Price</b></td>
  </tr>
  <?php
	$rowcnt = $_REQUEST['hdnrowcnt'];
	
	//echo "no: ".$_REQUEST['hdnrowcnt'];
	 
	for($z=1; $z<=$rowcnt; $z++){
		
		$cDesc= $_REQUEST['txtitemdesc'.$z];
		$nQty = $_REQUEST['txtnqty'.$z];
		$nAmount = $_REQUEST['txtnamount'.$z];
  ?>
  <tr>
    <td ><?php echo $nQty;?></td>
    <td ><?php echo $cDesc;?></td>
    <td align="right"><?php echo $nAmount;?></td>
  </tr>
  	<?php
    }
	?>
</table>

</div>

<div  class="content">
<input name="btnNew" type="button" value="New Transaction (ENTER)" class="btn" />
<br><br>
<span id="lblSum">Transaction Summary<br><?php echo $cSINo;?></span>
<br /><br />
<table width="60%" border="0" cellpadding="5px" align="center">
  <tr>
    <td class="td1">Credit Limit</td>
    <td class="td2"><?php echo $nLimit;?></td>
  </tr>
  <tr>
    <td class="td1">Total Amount</td>
    <td class="td2"><?php echo $nGross;?></td>
  </tr>
  <tr>
    <td class="td1">Credit Balance</td>
    <td class="td2"><?php echo $nLimitBal;?></td>
  </tr>
  <tr>
    <td class="td1">Amount Due</td>
    <td class="td2"><?php echo $nDue;?></td>
  </tr>
  <tr>
    <td class="td1">Amount Payed</td>
    <td class="td2"><?php echo $nPayed ;?></td>
  </tr>
  <tr>
    <td class="td1">Change</td>
    <td class="td2"><?php echo $nPayed-$nDue;?></td>
  </tr>
</table>
<br />

<!--<input name="btnNew" type="button" value="Cancel (ESC)" class="btn" onClick="window.location.href='trans.php?t=can&x=<?php// echo $cSINo;?>'"/>-->

</div>


</div>
</center>
</body>
</head>
</html>
<?php


  $autopostval = 1;
	if (!mysqli_query($con, "SELECT * FROM `parameters` WHERE ccode='POSPOST'")) {
		printf("Errormessage1: %s\n", mysqli_error($con));
	} 
					
	while($rowpst = mysqli_fetch_array($chkCustAcct, MYSQLI_ASSOC)){
		
			$autopostval = $rowpst['cvalue'];

	}



	if($autopostval==1){
		
		mysqli_query($con,"Update sales set lapproved=1 where compcode='$company' and ctranno='$cSINo'");

		mysqli_query($con,"INSERT INTO logfile(`ctranno`, `cuser`, `ddate`, `cevent`, `module`, `cmachine`, `cremarks`) 
			values('$cSINo','$preparedby',NOW(),'POSTED','POS RETAIL','$compname','Auto Post Record')");
		
		$status = "Posted";

	$date1 = date_format(date_create($dDelDate),"Y-m-d");
		
	//	SIEntry($cSINo);
	//	ToInv($cSINo,"POS","OUT",$date1);
		
		//Update items table stockonhands
		//$UpdateItem = mysqli_query($con,"Select A.citemno, B.citemdesc, B.nqty, A.nqty as nqtyin, A.nfactor as nfactorin From sales_t A left join items B on A.citemno=B.cpartno Where A.ctranno='$cSINo'");
		
	//	while($itmupdate = mysqli_fetch_array($UpdateItem, MYSQLI_ASSOC)){
			
	//		$itmpartno = $itmupdate['citemno'];
	//		$nstock = $itmupdate['nqty'] - ($itmupdate['nqtyin'] * $itmupdate['nfactorin']);
			
	//		mysqli_query($con,"Update items set nqty=$nstock where cpartno='$itmpartno'");
					
	//	}

	}

?>

<script type="text/javascript">
	$(document).keypress(function(e) {	 
	  if(e.keyCode == 13) {
		top.location.href="../SysFull/";
	  }
	 // if(e.keyCode == 27) {
		//top.location.href="";
	  //}
	});

</script>
