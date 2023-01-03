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
<title>Sales Per Customer</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Detailed</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th>Date</th>
    <th>Invoice No.</th>
    <th>Rank</th>
    <th>Customer Code</th>
    <th>Customer Name</th>
    <!--<th>Gross</th>-->
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$rpt = $_POST["seltype"];
$postz = $_POST["optradio"];
//echo $postz;
if($postz=="posted"){
	$qry = " and b.lapproved=1";
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

$sql = "select b.dcutdate,c.ccustomerclass, a.ctranno as csalesno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross
From sales_t a
left join sales b on a.ctranno=b.ctranno
left join customers c on b.ccode=c.cempid
left join items d on a.citemno=d.cpartno
where a.compcode='001' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0" .$qry. $qrytyp. "
order by b.dcutdate, a.ctranno";


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
	$totAmount=0;	
	$ngross = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['csalesno']){
			$invval = $row['csalesno'];
			$remarks = $row['cname'];
			$ccode = $row['ccode'];
			$crank = $row['ccustomerclass'];
			$ngross = $row['ngross'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php echo $invval;?></td>
    <td><?php echo $crank;?></td>
    <td><?php echo $ccode;?></td>
    <td><?php echo $remarks;?></td>
    <!--<td><?php //echo $ngross;?></td>-->
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo $row['nqty'];?></td>
    <td align="right"><?php echo $row['nprice'];?></td>
    <td align="right"><?php echo $row['namount'];?></td>
  </tr>
<?php 
		$invval = "";
		$remarks = "";
		$dateval="";		
		$classcode="";		
		$ngross = "";
		$crank = "";
		$ccode = "";
		$salesno=$row['csalesno'];
		$totAmount = $totAmount + $row['namount'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="10" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo $totAmount;?></b></td>
    </tr>
</table>

</body>
</html>