<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "StockLedger.php";

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sales Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Stock Ledger</h2>
<h3>for the period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th rowspan="2">Classification</th>
    <th colspan="2" rowspan="2">Product</th>
    <th rowspan="2">UOM</th>
    <th rowspan="2">Tran Type</th>
    <th rowspan="2">Trans No.</th>
    <th rowspan="2">Cutoff Date</th>
    <th colspan="3" class="cent">IN</th>
    <th colspan="3" class="cent">OUT</th>
    <th colspan="3" class="cent">RUNNING BALANCE</th>
  </tr>
  <tr>
  	<th class="cent">Qty</th>
    <th class="cent">Total Retail</th>
    <th class="cent">Total Cost</th>
    <th class="cent">Qty</th>
    <th class="cent">Total Retail</th>
    <th class="cent">Total Cost</th>
    <th class="cent">Qty</th>
    <th class="cent">Total Retail</th>
    <th class="cent">Total Cost</th>
  </tr>
  
<?php

$date1 = date_format(date_create($_POST["date1"]),"Y-m-d");
$date2 = date_format(date_create($_POST["date2"]),"Y-m-d");
$cid = $_POST["txtCustID"];

if($cid<>""){
 $qry = " and citemno='$cid'";
}
else{
 $qry = "";
}

$sql = "
		select a.ctranno, b.ddate, b.dreceived as dcutdate, a.citemno,  coalesce(sum(a.nqty*nfactor),0) as nqtyin, 0 as nqtyout
		from receive_t a 
		left join receive b on a.compcode=b.compcode and a.ctranno = b.ctranno 
		where a.compcode='001' and b.dreceived between '$date1' and '$date2' and a.citemno='$cid'
		and b.lcancelled=0
	
		UNION ALL

		select a.ctranno, b.ddate, b.dcutdate, a.citemno, 0 as nqtyin,  coalesce(sum(a.nqty*nfactor),0) as nqtyout
		from sales_t a 
		left join sales b on a.compcode=b.compcode and a.ctranno = b.ctranno 
		where a.compcode='001' and b.dcutdate between '$date1' and '$date2' and a.citemno='$cid'
		and b.lcancelled=0
		

		UNION ALL

		select a.ctranno, b.ddate, b.dreceived as dcutdate, a.citemno,  coalesce(sum(a.nqty*nfactor),0) as nqtyin, 0 as nqtyout
		from salesreturn_t a 
		left join salesreturn b on a.compcode=b.compcode and a.ctranno = b.ctranno 
		where a.compcode='001' and b.dreceived between '$date1' and '$date2' and a.citemno='$cid'
		and b.lcancelled=0
	
		UNION ALL

		select a.ctranno, b.ddate, b.dreturned as dcutdate, a.citemno, 0 as nqtyin, coalesce(sum(a.nqty*nfactor),0) as nqtyout
		from purchreturn_t a 
		left join purchreturn b on a.compcode=b.compcode and a.ctranno = b.ctranno 
		where a.compcode='001' and b.dreturned between '$date1' and '$date2' and a.citemno='$cid'
		and b.lcancelled=0
	 ";

	echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($class!=$row['cclass']){
			$classval=$row['cdesc'];
			$classcode="class='rpthead'";
		}
?>  
  <tr <?php echo $classcode;?>>
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td><?php echo $row['ctype'];?></td>
    <td><?php echo $row['ctranno'];?></td>
    <td><?php echo $row['dcutdate'];?></td>
    <td align="right"><?php if($row['nqtyin']<>0) { echo $row['nqtyin']; } ?></td>
    <td align="right"><?php if($row['nretailin']<>0) { echo $row['nretailin']; }?></td>
    <td align="right"><?php if($row['ncostin']<>0) { echo $row['ncostin']; }?></td>
    <td align="right"><?php if($row['nqtyout']<>0) { echo $row['nqtyout']; }?></td>
    <td align="right"><?php if($row['nretailout']<>0) { echo $row['nretailout']; }?></td>
    <td align="right"><?php if($row['ncostout']<>0) { echo $row['ncostout']; }?></td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
<?php 
$class=$row['cclass'];
$classval="";
$classcode="";

		//$totCost = $totCost + $row['ncost'];
		//$totPrice = $totPrice + $row['nprice'];
	}
?>
</table>

</body>
</html>