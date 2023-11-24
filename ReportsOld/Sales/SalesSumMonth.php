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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sales Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Summary: Per Month</h2>
<h3>For the Year <?php echo $_POST["selmonth"];?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px">
  <tr>
    <th rowspan="2">Classification</th>
    <th colspan="2" rowspan="2">Product</th>
    <th rowspan="2">UOM</th>
    <?php
    	for ($x=1; $x<=12; $x++){
		
	?>
    <td colspan="2" align="center"  style="border-left:solid 1px #039;"><b><?php echo date("F", mktime(0, 0, 0, $x, 10))?></b></td>
    <?php } ?>
  </tr>
  

  <tr>
        <?php
    	for ($x=1; $x<=12; $x++){
		
	?>
    <th style="border-left:solid 1px #039;">Qty</th>
  	<th>Total Amount</th>
    <!--<th>Total Cost</th>-->
    <?php } ?>
  </tr>
  
<?php

$selyr = $_POST["selmonth"];

$sql = "select A.dmonth, A.cclass, A.cdesc, A.citemno, A.citemdesc, A.cunit, sum(A.nqty) as nqty, sum(A.nprice*A.nqty) as nprice, sum(A.ncost*A.nqty) as ncost
FROM
(
select MONTH(b.dcutdate) as dmonth, d.cclass, c.cdesc, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, 0 as ncost
From sales_t a
left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
left join groupings c on d.cclass=c.ccode and c.ctype='ITEMCLS'
where a.compcode='$company' and YEAR(b.dcutdate) = '$selyr' and b.lcancelled=0 and b.lapproved=1
) A
group by A.dmonth, A.cclass, A.cdesc,A.citemno, A.citemdesc, A.cunit
order by A.cclass, A.citemdesc, A.dmonth ";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	
	$row1 = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$citemno = $row1["citemno"];
	$citemdesc = $row1["citemdesc"];
	$cunit = $row1["cunit"];
	
	  for ($x=1; $x<=12; $x++){
		  
		  $myarrqty[$x] = 0;
		  $myarrret[$x] = 0;
		  $myarrcost[$x] = 0;
		
	  }

				  $myarrqty[$row1['dmonth']] = $row1['nqty'];
				  $myarrret[$row1['dmonth']] = $row1['nprice'];
				  $myarrcost[$row1['dmonth']] = $row1['ncost'];

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($class!=$row['cclass']){
			$classval=$row['cdesc'];
			$classcode="class='rpthead'";
		}
		
		if($citemno==$row['citemno']){
			
				  
				  $myarrqty[$row['dmonth']] = $row['nqty'];
				  $myarrret[$row['dmonth']] = $row['nprice'];
				  $myarrcost[$row['dmonth']] = $row['ncost'];
				
			
		}
		
		else{
?>  
  <tr <?php echo $classcode;?>>
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $citemno;?></td>
    <td nowrap><?php echo $citemdesc;?></td>
    <td><?php echo $cunit;?></td>
    
    <?php
    	for ($x=1; $x<=12; $x++){
		
	?>

            <td align="right"  style="border-left:solid 1px #039;"><?php echo (($myarrqty[$x] > 0) ? number_format($myarrqty[$x]) : '');?></td>
            <td align="right"><?php echo (($myarrret[$x] > 0) ? number_format($myarrret[$x],2) : '') ;?></td>
           <!-- <td align="right"><?php //echo (($myarrcost[$x] > 0) ? $myarrcost[$x] : '') ;?></td>-->
    
     <?php 

		}
		
			  for ($x=1; $x<=12; $x++){
		  
				  $myarrqty[$x] = 0;
				  $myarrret[$x] = 0;
				  $myarrcost[$x] = 0;
		
			  }

		
				  $myarrqty[$row['dmonth']] = $row['nqty'];
				  $myarrret[$row['dmonth']] = $row['nprice'];
				  $myarrcost[$row['dmonth']] = $row['ncost'];
				

	$citemno = $row["citemno"];
	$citemdesc = $row["citemdesc"];
	$cunit = $row["cunit"];

		}
		 
	  ?>
  </tr>
<?php 
$class=$row['cclass'];
$classval="";
$classcode="";

		$totCost = $totCost + $row['ncost'];
		$totPrice = $totPrice + $row['nprice'];
	}
?>

  <tr <?php echo $classcode;?>>
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $citemno;?></td>
    <td nowrap><?php echo $citemdesc;?></td>
    <td><?php echo $cunit;?></td>
    
    <?php
    	for ($x=1; $x<=12; $x++){
		
	?>

            <td align="right"  style="border-left:solid 1px #039;"><?php echo (($myarrqty[$x] > 0) ? number_format($myarrqty[$x]) : '');?></td>
            <td align="right"><?php echo (($myarrret[$x] > 0) ? number_format($myarrret[$x],2) : '') ;?></td>
            <!--<td align="right"><?php //echo (($myarrcost[$x] > 0) ? $myarrcost[$x] : '') ;?></td>-->
    
     <?php 

		}
		 
	  ?>
  </tr>
</table>

</body>
</html>