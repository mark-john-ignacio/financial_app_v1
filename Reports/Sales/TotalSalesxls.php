<?php
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=TotalSales.xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);

if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');

$date1 = $_REQUEST["date1"];
$date2 = $_REQUEST["date2"];
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Total Sales (Gross)</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Total Sales Report</h2>
<h3>For the Period <?php echo date_format(date_create($date1),"F d, Y");?> to <?php echo date_format(date_create($date2),"F d, Y");?></h3>
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


$sql ="
select  A.dcutdate, A.ddate, A.csalesno, A.ccode, A.cname, A.ncreditlimit, A.ncreditbal, A.ngross, A.ccustomerclass
FROM(
select  a.dcutdate, a.ddate, a.csalesno, a.ccode, IFNULL(c.ctradename,c.cname) as cname, a.ncreditlimit, a.ncreditbal, a.ngross, c.ccustomerclass
From sales a
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.ncreditbal <= a.ncreditlimit and lcancelled=0 and lapproved=1


UNION ALL

select  a.dcutdate, a.ddate, a.csalesno, a.ccode, IFNULL(c.ctradename,c.cname) as cname, a.ncreditlimit, a.ncreditbal, a.ngross, c.ccustomerclass
From salesback a
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.ncreditbal <> 0 and lcancelled=0 and lapproved=1
) A

order by A.ccode, A.dcutdate
";

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
    <td><?php echo $row["csalesno"];?></td>
    <td align="right"><?php echo number_format($row["ncreditlimit"],4);?></td>
    <td align="right"><?php echo number_format($row["ncreditbal"],4);?></td>
    <td align="right"><?php echo number_format($row["ngross"],4);?></td>
    <td align="right">
	<b>
	<?php 
	//if($row["ncreditbal"] > $row["ngross"]){
		
		
	//		echo number_format($row["ngross"],4);
			
			
	//}
	//else{
	//	if($row["ncreditbal"]==0){
	//		
			echo number_format($row["ncreditlimit"],4);
			$totCredit = $totCredit + $row["ngross"];
			
	//	}
	//	else{

	//	    echo number_format($row["ncreditbal"],4);
	//	}
			
	//		$totCredit = $totCredit + $row["ncreditbal"];
	//}
?>
</b></td>
    
  </tr>
 
  <?php 
		
		  
		  }

  
   ?>

   <tr>
     <td colspan="7" align="right"><b>TOTAL CREDIT AMOUNT: </b></td>
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

$sql = "select  a.dcutdate, a.ddate, a.csalesno, a.ccode, c.cname, a.ncreditlimit, a.ncreditbal, a.ngross, c.ccustomerclass
From sales a
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.npayed <> 0 and lcancelled=0 and lapproved=1
order by a.ccode, a.dcutdate";

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
    <td><?php echo $row["csalesno"];?></td>
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
     <td colspan="7"  align="right"><b>TOTAL PAYED AMOUNT: </b></td>
     <td align="right" style="border-top:2px solid #000000; border-bottom:5px double #000000"><b><?php echo number_format($totPayed,4);?></b></td>
   </tr>

</table>



</body>
</html>