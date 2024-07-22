<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PRMonitoring";
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
<title>PR Monitoring</title>

<style>
	table, th, td {
		white-space: nowrap !important;
	}
</style>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Purchase Request Monitoring</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px" id="MyTable">
  <tr>
    <th>Date</th>
    <th>PR No.</th>
    <th>Section</th>
    <th>Item Code</th>
	<th>Part No.</th>
	<th>Description</th>
	<th>Remarks</th>
    <th>UOM</th>
    <th>PR Qty</th>
    <th>PO Qty</th>
    <th>Balance</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$rpt = $_POST["slesections"];
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
	$qrytyp = " and b.locations_id='$rpt'";
}

$arrPO = array();
$result=mysqli_query($con,"Select A.creference, A.nrefident, A.citemno, sum(A.nqty) as nqty From purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono where A.compcode='".$company."' and B.lcancelled=0 and B.lapproved=1 and B.lvoid=0 Group By A.creference, A.nrefident, A.citemno");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}

$sql = "select b.dneeded as dcutdate, a.ctranno, b.locations_id, c.cdesc as csection, a.nident, a.citemno, a.citemdesc, a.cpartdesc, a.cunit, a.nqty, a.cremarks
From purchrequest_t a
left join purchrequest b on a.compcode=b.compcode and a.ctranno=b.ctranno
left join locations c on b.compcode=c.compcode and b.locations_id=c.nid
where a.compcode='".$company."' and b.dneeded between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid=0" .$qry. $qrytyp. "
order by b.dneeded, a.ctranno";


//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{

		//find PO reference
		$POQty = 0;
		foreach($arrPO as $rowPO){
			//echo $row['ctranno']."==".$rowPO['creference'] ."&&". $row['citemno']."==".$rowPO['citemno'] ."&&". $row['nident']."==".$rowPO['nrefident']."<br>";
			if($row['ctranno']==$rowPO['creference'] && $row['citemno']==$rowPO['citemno'] && $row['nident']==$rowPO['nrefident']){
				$POQty = $POQty + floatval($rowPO['nqty']);
			}
		}
		
		$cxBal = floatval($row['nqty']) - floatval($POQty);

		if($cxBal > 0){
?>  
  <tr>
    <td><?=$row['dcutdate'];?></td>
    <td><?=$row['ctranno'];?></td>
    <td><?=$row['csection'];?></td>
    <td><?=$row['citemno'];?></td>
	<td><?=$row['cpartdesc'];?></td>
    <td><?=$row['citemdesc'];?></td>
    <td><?=$row['cremarks'];?></td>
    <td><?=$row['cunit'];?></td>
    <td align="right"><?=number_format($row['nqty'],2);?></td>
    <td align="right"><?=number_format($POQty,2);?></td>
	<td align="right"><?=number_format($cxBal,2);?></td>
  </tr>
<?php 
		}
	}
?>

</table>

</body>
</html>
