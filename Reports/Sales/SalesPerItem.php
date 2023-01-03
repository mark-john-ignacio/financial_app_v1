<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesPerItem.php";

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
<title>Sales Per Item</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Per Item</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
<h3><?php echo $_POST["txtCust"];?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th>Date</th>
    <th>Invoice No.</th>
    <th colspan="2">Customer</th>
    <th>Qty/Uom</th>
    <th>Price</th>
    <th>Disc(%)</th>
    <th>Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$custid = $_POST["txtCustID"];

$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount,A.namount, A.lapproved
FROM
(
select b.dcutdate, a.ctranno as csalesno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice,a.ndiscount, a.namount, b.lapproved
From sales_t a
left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
where a.compcode='$company' and a.citemno='$custid' and b.lcancelled=0
) A
Where A.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
order by A.dcutdate, A.csalesno";

//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$ddate = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$totQty=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($ddate!=$row['dcutdate']){
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php 
	
	echo $row['csalesno'];
		if($row['lapproved']==0){
			echo "<i>(Pending)</i>";
		}
	?></td>
    <td><?php echo $row['ccode'];?></td>
    <td><?php echo $row['cname'];?></td>
    <td align="right"><?php echo $row['nqty']." ".$row['cunit'];?></td>
    <td align="right"><?php echo number_format($row['nprice'],4);?></td>
    <td align="right"><?php echo $row['ndiscount'];?></td>
    <td align="right"><?php echo number_format($row['namount'],4);?></td>
  </tr>
<?php 
		$dateval="";		
		$classcode="";		
		$ddate=$row['dcutdate'];
		$totAmount = $totAmount + $row['namount'];
		
		$totQty = $totQty + $row['nqty'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="4" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right"><b><?php echo number_format($totQty,4);?></td>
    	<td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right"><b><?php echo number_format($totAmount,4);?></b></td>
    </tr>
</table>

</body>
</html>