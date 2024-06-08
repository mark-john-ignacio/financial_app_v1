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
<h2>Purchased Order Report</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px" id="MyTable">
  <tr>
    <th>Date</th>
    <th>Voucher No.</th>
    <th>Links</th>
    <th>Supplier</th>
    <th>Code</th>
	<th>Name</th>
	<th>Description</th>
    <th>UOM</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Amount</th>	
    <th>Currency</th>
	<th>PO Due Date</th>
	<th>Status</th>
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


$sql = "select b.dpodate as dcutdate, a.cpono as ctranno, b.ccode, c.cname, a.creference, a.citemno, a.cpartno, a.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross, b.lapproved, b.ccurrencycode, e.nintval as nterms
From purchase_t a
left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
left join groupings e on b.compcode=e.compcode and b.cterms=e.ccode
where a.compcode='".$company."' and b.dpodate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid=0" .$qry. $qrytyp. "
order by b.dpodate, a.cpono";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$dxdate = "";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if(intval($row['nterms']) > 0){
			$dxdate = date('Y-m-d', strtotime($row['dcutdate']. ' + '.$row['nterms'].' days'));
		}else{
			$dxdate = $row['dcutdate'];
		}	
?>  
  <tr>
    <td><?=$row['dcutdate'];?></td>
    <td><?=$row['ctranno'];?></td>
    <td><?=$row['creference'];?></td>
    <td><?=$row['cname'];?></td>
    <td><?=$row['citemno'];?></td>
	<td><?=$row['cpartno'];?></td>
    <td><?=$row['citemdesc'];?></td>
    <td><?=$row['cunit'];?></td>
    <td align="right"><?=number_format($row['nqty'],2);?></td>
    <td align="right"><?=number_format($row['nprice'],2);?></td>
    <td align="right"><?=number_format($row['namount'],2);?></td>  
	<td><?=$row['ccurrencycode'];?></td>
	<td align="center"><?=$dxdate;?></td>
    <td align="center"><?=($row['lapproved']==1) ? "APPROVED" : "PENDING"?></td>
  </tr>
<?php 

	}
?>

</table>

</body>
</html>
