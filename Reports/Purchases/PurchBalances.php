<?php
	if(!isset($_SESSION)){
		session_start();
	}
	include('../../Connection/connection_string.php');
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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PO Balances</title>

<style>
	table, th, td {
		white-space: nowrap !important;
	}
</style>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>PO Balances</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px">
  <tr>
    <th>PO No.</th>
    <th>PO Date</th>
    <th>Supplier Code</th>
    <th>Supplier Name</th>
    <!--<th>Gross</th>-->
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th>PO Qty</th>
    <th>Total RR Qty</th>
		<th>Variance</th>
    <!--
		<th>PO Price</th>
		<th>PO Amount</th>
		-->
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$datefil = $_POST["seltype"];

$arrPO = array();
$result=mysqli_query($con,"Select A.creference, A.nrefidentity, A.citemno, sum(A.nqty) as nqty From receive_t A left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='".$company."' and B.lvoid=0 and B.lcancelled=0 Group By A.creference, A.nrefidentity, A.citemno");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}

$sql = "select a.cpono as ctranno, b.".$datefil." as ddate, b.ccode, c.cname, a.nident, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
From purchase_t a
left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
where a.compcode='".$company."' and DATE(b.".$datefil.") between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lapproved = 1
order by b.".$datefil.", a.cpono";


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
			$dateval= date_format(date_create($row['ddate']),"m/d/Y");
			$classcode="class='rpthead'";
		}

		//find PO reference
		$RRQty = 0;
		foreach($arrPO as $rowPO){
			if($rowPO['creference']==$row['ctranno'] && $rowPO['citemno']==$row['citemno'] && $rowPO['nrefidentity']==$row['nident']){
				$RRQty = $rowPO['nqty'];
			}
		}

		if(floatval($row['nqty'])!==floatval($RRQty)){
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $invval;?></td>
		<td><?php echo $dateval;?></td>
    <td><?php echo $ccode;?></td>
    <td><?php echo $remarks;?></td>
    <!--<td><?php //echo $ngross;?></td>-->
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="center"><?php echo number_format($row['nqty']);?></td>
		<td align="center"><?php echo number_format($RRQty);?></td>
		<td align="center"><?php echo number_format(floatval($row['nqty'])-floatval($RRQty));?></td>
		<!--
    <td align="right"><?//php echo number_format($row['nprice'],2);?></td>
    <td align="right"><?//php echo number_format($row['namount'],2);?></td>
		-->
  </tr>
<?php 
		}

		$invval = "";
		$remarks = "";
		$dateval="";
		$ccode = "";		
		$classcode="";		
		$salesno=$row['ctranno'];
	}
?>

    
</table>

</body>
</html>