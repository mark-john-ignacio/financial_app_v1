<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PurchDetailed";
	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
	
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
		
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Purchased Detailed</title>

<style>
	table, th, td {
		white-space: nowrap !important;
	}
</style>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Purchased Detailed</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px" id="MyTable">
  <tr>
    <th>Date</th>
    <th>WRR No.</th>
    <th>Supplier Code</th>
    <th>Supplier Name</th>
    <!--<th>Gross</th>-->
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th>RR Qty</th>
    <th>PO Price</th>
    <th>PO Amount</th>
		<th>SI Price</th>
    <th>SI Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$rpt = $_POST["seltype"];
$postz = $_POST["sleposted"];

//echo $postz;
if($postz!==""){
	$qry = " and b.lapproved=".$postz;
}
else{
	$qry = "";
}

$qrytyp = "";
if($rpt==""){
	$qrytyp = "";
}else{
	$qrytyp = " and d.ctype='$rpt'";
}

$arrPO = array();
$result=mysqli_query($con,"Select A.cpono, A.nident, A.citemno, A.nprice, A.namount From purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono where A.compcode='".$company."' and B.lcancelled=0 and B.lapproved=1 and B.lvoid=0");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}


$arrSI = array();
$result=mysqli_query($con,"Select A.creference, A.nrefidentity, A.citemno, A.nprice, A.namount From suppinv_t A left join suppinv B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='".$company."' and B.lcancelled=0 and B.lvoid=0");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrSI[] = $row;
}

$sql = "select b.dreceived as dcutdate, a.ctranno, b.ccode, c.cname, a.nident, a.nrefidentity, a.creference, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross
From receive_t a
left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
where a.compcode='".$company."' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid=0" .$qry. $qrytyp. "
order by b.dreceived, a.ctranno";


//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$TOTPOAmt=0;	
	$TOTSIAmt=0;
	$ngross = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['ctranno']){
			$invval = $row['ctranno'];
			$remarks = $row['cname'];
			$ccode = $row['ccode'];
			$ngross = $row['ngross'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}

		//find PO reference
		$POPrice = 0;
		foreach($arrPO as $rowPO){
			if($rowPO['cpono']==$row['creference'] && $rowPO['citemno']==$row['citemno'] && $rowPO['nident']==$row['nrefidentity']){
				$POPrice = $rowPO['nprice'];
			}
		}

		$POAmt = floatval($row['nqty']) * floatval($POPrice);


		//find Suppliers Invoice
		$SIPrice = 0;
		foreach($arrSI as $rowSI){
			if($rowSI['creference']==$row['ctranno'] && $rowSI['citemno']==$row['citemno'] && $rowSI['nrefidentity']==$row['nident']){
				$SIPrice = $rowSI['nprice'];
			}
		}

		$SIAmt = floatval($row['nqty']) * floatval($SIPrice);
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php echo $invval;?></td>
    <td><?php echo $ccode;?></td>
    <td><?php echo $remarks;?></td>
    <!--<td><?php //echo $ngross;?></td>-->
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo number_format($row['nqty']);?></td>
    <td align="right"><?php echo number_format($POPrice,2);?></td>
    <td align="right"><?php echo number_format($POAmt,2);?></td>
		<td align="right"><?php echo number_format($SIPrice,2);?></td>
    <td align="right"><?php echo number_format($SIAmt,2);?></td>
  </tr>
<?php 
		$invval = "";
		$remarks = "";
		$dateval="";
		$ccode = "";		
		$classcode="";		
		$ngross = "";
		$salesno=$row['ctranno'];
		$TOTPOAmt = $TOTPOAmt + floatval($POAmt);	
		$TOTSIAmt = $TOTSIAmt + floatval($SIAmt);
	}
?>

    <tr class='rptGrand'>
    	<td colspan="6" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
      <td align="right"><b><?php echo number_format($TOTPOAmt,2);?></b></td>
			<td align="right">&nbsp;</td>
			<td align="right"><b><?php echo number_format($TOTSIAmt,2);?></b></td>
    </tr>
</table>

</body>
</html>

<script type="text/javascript">
$( document ).ready(function() {

	$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
});
</script>