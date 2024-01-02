<?php
if(!isset($_SESSION)){
session_start();
}
//$_SESSION['pageid'] = "TotalSales.php";

include('../../Connection/connection_string.php');
//include('../../include/denied.php');
//include('../../include/access2.php');

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
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
<title>Total Sales (Gross)</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Total Sales Report</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
<br><br>
<a href="TotalSalesxls.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>" target="_blank">Extract To Excel</a>
</center>

<br><br>
<table width="80%" border="0" align="center">
  <tr>
    <th colspan="8">CREDITED SALES</th>
  </tr>
  <tr>
    <th>Date</th>
    <th>Customer</th>
    <th>Classification</th>
    <th>Sales No.</th>
    <th>Credit Limit</th>
    <th>Credit Balance</th>
    <th>Total Sales</th>
    <th>Credited Amount</th>
  </tr>
  
<?php


$sql = "
select  A.dcutdate, A.ddate, A.ctranno, A.ccode, A.cname, A.ncreditlimit, A.ncreditbal, A.ngross, A.ccustomerclass
FROM(
select  a.dcutdate, a.ddate, a.ctranno, a.ccode, c.cname, c.nlimit as ncreditlimit, b.ncreditbal, a.ngross, c.ccustomerclass
From sales a
left join sales_t_dues b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and lvoid=0 and lcancelled=0 and lapproved=1


UNION ALL

select  a.dcutdate, a.ddate, a.ctranno, a.ccode, c.cname, c.nlimit as ncreditlimit, b.ncreditbal, a.ngross, c.ccustomerclass
From salesback a
left join sales_t_dues b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and lcancelled=0 and lapproved=1 and lvoid=0
) A

order by A.ccode, A.dcutdate, A.ddate
";

//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$totCredit = 0;
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{

		
?>
   <tr>
    <td><?php echo date_format(date_create($row["ddate"]),"m/d/Y h:i:s A");?></td>
    <td><?php echo $row["ccode"];?> - <?php echo $row["cname"];?></td>
    <td><?php echo $row["ccustomerclass"];?></td>
    <td><?php echo $row["ctranno"];?></td>
    <td align="right"><?php echo number_format($row["ncreditlimit"],4);?></td>
    <td align="right"><?php echo number_format($row["ncreditbal"],4);?></td>
    <td align="right"><?php echo number_format($row["ngross"],4);?></td>
    <td align="right">
	<b>
	<?php 
	if($row["ncreditbal"] > $row["ngross"]){
			echo number_format($row["ngross"],4);
			
			$totCredit = $totCredit + $row["ngross"];
	}
	else{
		if($row["ncreditbal"]==0){
			
			echo number_format($row["ncreditlimit"],4);
			$totCredit = $totCredit + $row["ngross"];
			
		}
		else{

		    echo number_format($row["ncreditbal"],4);
		}
			
			$totCredit = $totCredit + $row["ncreditbal"];
	}
?>
</b></td>
    
  </tr>
 
  <?php 
		
		  
		  }

  
   ?>

   <tr>
     <td colspan="7" align="right" style="padding-right: 10px"><b>TOTAL CREDIT AMOUNT </b></td>
     <td align="right" style="border-top:2px solid #000000; border-bottom:5px double #000000"><b><?php echo number_format($totCredit,4);?></b></td>
   </tr>

</table>
<br><br>
<table width="80%" border="0" align="center">
  <tr>
    <th colspan="8">EXCESS SALES</th>
  </tr>
  <tr>
    <th>Date</th>
    <th>Customer</th>
    <th>Classification</th>
    <th>Sales No.</th>
    <th>Credit Limit</th>
    <th>Credit Balance</th>
    <th>Total Sales</th>
    <th>Payed Amount</th>
  </tr>
  <?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$company = $_SESSION['companyid'];

$sql = "select  a.dcutdate, a.ddate, a.ctranno, a.ccode, c.cname, c.nlimit as ncreditlimit, b.ncreditbal, a.ngross, c.ccustomerclass
From sales a
left join sales_t_dues b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.npayed <> 0 and lcancelled=0 and lapproved=1
order by a.ccode, a.dcutdate, a.ddate";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$totPayed = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{

		
?>
  <tr>
    <td><?php echo date_format(date_create($row["ddate"]),"m/d/Y h:i:s A");?></td>
    <td><?php echo $row["ccode"];?> - <?php echo $row["cname"];?></td>
    <td><?php echo $row["ccustomerclass"];?></td>
    <td><?php echo $row["ctranno"];?></td>
    <td align="right"><?php echo number_format($row["ncreditlimit"],4);?></td>
    <td align="right"><?php echo number_format($row["ncreditbal"],4);?></td>
    <td align="right"><?php echo number_format($row["ngross"],4);?></td>
    <td align="right"><b>
      <?php 
	if($row["ncreditbal"] > $row["ngross"]){
			echo number_format($row["ngross"],4);
			$totPayed = $totPayed + $row["ngross"];
	}
	else{
		    echo number_format($row["ngross"] - $row["ncreditbal"],4);
			$totPayed = $totPayed + ($row["ngross"] - $row["ncreditbal"]);
	}
?>
    </b></td>
  </tr>
  <?php 
		
		  
		  }

  
   ?>

   <tr>
     <td colspan="7"  align="right" style="padding-right: 10px"><b>TOTAL PAYED AMOUNT </b></td>
     <td align="right" style="border-top:2px solid #000000; border-bottom:5px double #000000"><b><?php echo number_format($totPayed,4);?></b></td>
   </tr>

</table>



</body>
</html>