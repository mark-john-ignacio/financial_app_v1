<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PurchSummary";

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
<title>Purchase Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Purchased Summary: Per Transaction</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <tr>
  	<th>Transaction No.</th>
  	<th>Date</th>
    <th colspan="2">Supplier</th>
    <th style="text-align: right">Total Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$postz = $_POST["sleposted"];

if($postz!==""){
	$qry = "and a.lapproved=".$postz;
}
else{
	$qry = "";
}

$sql = "select a.ccode, b.cname, a.ngross as namnt, a.ctranno as csalesno, a.dreceived as dcutdate
From suppinv a
left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode
where a.compcode='$company' and a.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lcancelled=0 and a.lvoid=0 ".$qry." order by a.ctranno";

//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$totPrice=0;	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
?>  
  <tr >
    <td><?php echo $row['csalesno'];?></td>
    <td><?php echo $row['dcutdate'];?></td>
    <td><?php echo $row['ccode'];?></td>
    <td><?php echo utf8_encode($row['cname']);?></td>
    <td align="right"><?php echo number_format($row['namnt'],2);?></td>
  </tr>
<?php 

		$totPrice = $totPrice + $row['namnt'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="4" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right"><b><?php echo number_format($totPrice,2);?></b></td>
    </tr>
</table>

</body>
</html>

<script type="text/javascript">
$( document ).ready(function() {

	$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
});
</script>