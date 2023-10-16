<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesSummary.php";

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
<h2>Purchased Summary: Per Item</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <tr>
  	<th>Classification</th>
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th style="text-align: right" nowrap>Ave. Purchase / Month</th>
    <th style="text-align: right">Qty</th>
    <th style="text-align: right">Total Amount</th>
  </tr>
  
<?php
$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$postz = $_POST["sleposted"];

if($postz!==""){
	$qry = " and b.lapproved=".$postz;
}
else{
	$qry = "";
}

$arrPO = array();
$result=mysqli_query($con,"Select A.cpono, A.nident, A.citemno, A.nprice, A.namount From purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono where A.compcode='".$company."' and B.lcancelled=0 and B.lapproved=1");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}


$arrSI = array();
$result=mysqli_query($con,"Select A.creference, A.nrefidentity, A.citemno, A.nprice, A.namount From suppinv_t A left join suppinv B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='".$company."' and B.lcancelled=0");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrSI[] = $row;
}



$mnths = (int)abs((strtotime($date1) - strtotime($date2))/(60*60*24*30)) + 1;
//$rpt = $_POST["selrpt"];

$sql = "select A.cclass, A.cdesc, A.citemno, A.citemdesc, A.cunit, sum(A.nqty) as nqty, sum(A.nprice*A.nqty) as nprice, sum(A.ncost*A.nqty) as ncost
FROM
(
select d.cclass, c.cdesc, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, 0 as ncost
From suppinv_t a
left join suppinv b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
left join groupings c on d.cclass=c.ccode and a.compcode=c.compcode and c.ctype='ITEMCLS'
where a.compcode='$company' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0".$qry."
) A
group by A.cclass, A.cdesc,A.citemno, A.citemdesc, A.cunit
order by A.cclass, sum(a.nqty) DESC";

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
    <td nowrap><?php echo $row['cunit'];?></td>
    <td align="right" style="padding-right: 10px"><?php echo number_format(floatval($row['nqty']) / $mnths,2);?></td>
    <td align="right" style="padding-right: 10px"><?php echo number_format($row['nqty']);?></td>
    <td align="right" style="padding-right: 10px"><?php echo number_format($row['nprice'],2);?></td>
  </tr>
<?php 
$class=$row['cclass'];
$classval="";
$classcode="";

		$totCost = $totCost + $row['ncost'];
		$totPrice = $totPrice + $row['nprice'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="4" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right">&nbsp;</td>
    	<td align="right">&nbsp;</td>
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