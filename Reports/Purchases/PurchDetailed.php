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
<title>Purchased Detailed</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Purchased Detailed</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th>Date</th>
    <th>WRR No.</th>
    <th>Supplier Code</th>
    <th>Supplier Name</th>
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

$sql = "select b.dreceived as dcutdate, a.ctranno as csalesno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross
From receive_t a
left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
where a.compcode='001' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0" .$qry. $qrytyp. "
order by b.dreceived, a.ctranno";


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
			$ngross = $row['ngross'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php echo $invval;?></td>
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
		$ccode = "";		
		$classcode="";		
		$ngross = "";
		$salesno=$row['csalesno'];
		$totAmount = $totAmount + $row['namount'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="9" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo $totAmount;?></b></td>
    </tr>
</table>

</body>
</html>