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
<h2>Employee Sales Summary</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
<br><br>
<a href="TotalSalesEmpxls.php?date1=<?php echo $date1;?>&date2=<?php echo $date2;?>" target="_blank">Extract To Excel</a>
</center>

<br><br>
<table width="80%" border="0" align="center">
  <tr>
    <th>&nbsp;</th>
    <th>Date</th>
    <th>Customer</th>
    <th>Classification</th>
    <th>Credit Limit</th>
    <th>Total Sales</th>
  </tr>
  
<?php


$sql = "
select A.dcutdate, A.ccode, A.cname, A.ncreditlimit, Sum(A.ngross) as ngross, A.ccustomerclass
FROM
(
select  a.dcutdate,a.ccode, c.cname, a.ncreditlimit, a.ngross, c.ccustomerclass
From sales a
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')

UNION ALL

select  a.dcutdate,a.ccode, c.cname, a.ncreditlimit, a.ngross, c.ccustomerclass
From salesback a
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')

) A
Group by A.dcutdate, A.ccode, A.cname, A.ncreditlimit, A.ccustomerclass
order by A.dcutdate, A.ccode
";

//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$all_course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$date1 = date_format(date_create($all_course_data["dcutdate"]),"m/d/Y");
	
	
	
	$totCredit = 0;
	$cntr = 0;
	$date2 = "";
//echo $date1."-".$date2."<br>"; 

$result2=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result2, MYSQLI_ASSOC))
	{
	$cntr = $cntr + 1;
	$date2 = date_format(date_create($row["dcutdate"]),"m/d/Y");
	
		if($date1!=$date2 && $cntr > 1){
			$date1 = $date2;
			$cntr = 1;

?>
           <tr>
             <td colspan="5" align="right"><b>TOTAL AMOUNT: </b></td>
             <td align="right" style="border-top:2px solid #000000; border-bottom:5px double #000000"><b><?php echo number_format($totCredit,4);?></b></td>
           </tr>
           <tr>
             <td colspan="6" align="right">&nbsp;</td>
           </tr>

<?php
			
		$totCredit = 0;

		}
	
		$totCredit =  $totCredit + $row["ngross"];
		
		
	
?>
   <tr>
    <td><?php echo $cntr;?></td>
    <td><?php echo date_format(date_create($row["dcutdate"]),"m/d/Y");?></td>
    <td><?php echo $row["ccode"];?> - <?php echo $row["cname"];?></td>
    <td><?php echo $row["ccustomerclass"];?></td>
    <td align="right"><?php echo number_format($row["ncreditlimit"],4);?></td>
    <td align="right"><?php echo number_format($row["ngross"],4);?></td>    
  </tr>
 
  <?php 
//echo $date1."-".$date2."<br>"; 
		  }

   ?>

   <tr>
     <td colspan="5" align="right"><b>TOTAL AMOUNT: </b></td>
     <td align="right" style="border-top:2px solid #000000; border-bottom:5px double #000000"><b><?php echo number_format($totCredit,4);?></b></td>
   </tr>

</table>



</body>
</html>