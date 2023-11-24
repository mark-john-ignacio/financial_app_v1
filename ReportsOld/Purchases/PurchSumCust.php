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
<title>Purchase Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Purchase Summary: Per Supplier</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th colspan="2">Supplier</th>
    <th width="100">Total Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$sql = "select a.ccode, b.cname, sum(a.ngross) as namnt
From receive a
left join suppliers b on a.ccode=b.ccode
where a.compcode='001' and a.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lcancelled=0 and a.lapproved=1
group by a.ccode, b.cname
order by b.cname";

//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$totPriceG=0;	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
?>  
  <tr>
    <td><?php echo $row['ccode'];?></td>
    <td><?php echo utf8_encode($row['cname']);?></td>
    <td align="right"><?php echo $row['namnt'];?></td>
  </tr>
<?php 

		$totPriceG = $totPriceG + $row['namnt'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="2" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right"><b><?php echo $totPriceG;?></b></td>
    </tr>
</table>

</body>
</html>