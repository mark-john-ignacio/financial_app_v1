<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PurchPerSupp";
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
<title>Purchase Per Supplier</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Purchases Per Supplier</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
<h3><?php echo $_POST["txtCust"];?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <tr>
    <th>Date</th>
    <th>Invoice No.</th>
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th style="text-align: right">Qty</th>
    <th style="text-align: right">Price</th>
    <th style="text-align: right">Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$custid = $_POST["txtCustID"];
$postedtran = $_POST["sleposted"];

$qryposted = "";
if($postedtran!==""){
	$qryposted = "and b.lapproved=".$postedtran."";
}

$sql = "select b.dreceived as dcutdate, a.ctranno as csalesno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.lapproved
From suppinv_t a
left join suppinv b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join suppliers c on b.ccode=c.ccode and a.compcode=c.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
where a.compcode='$company' and b.lvoid = 0 and b.ccode='$custid' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 ".$qryposted." order by b.dreceived, a.ctranno";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$salesno = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['csalesno']){
			$invval = $row['csalesno'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php echo $invval;?></td>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo number_format($row['nqty']);?></td>
    <td align="right"><?php echo number_format($row['nprice'],2);?></td>
    <td align="right"><?php echo number_format($row['namount'],2);?></td>
  </tr>
<?php 
		$invval = "";
		$dateval="";		
		$classcode="";		
		$salesno=$row['csalesno'];
		$totAmount = $totAmount + $row['namount'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="7" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo number_format($totAmount,2);?></b></td>
    </tr>
</table>

</body>
</html>

<script type="text/javascript">
$( document ).ready(function() {

	$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
});
</script>