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
	<link rel="stylesheet" type="text/css" href="../../	Bootstrap/css/bootstrap.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sales Summary</title>
</head>

<body style="padding:10px">
<center>
<h3 class="noppadding"><?php echo strtoupper($compname);  ?></h3>
<h3 class="noppadding">Sales Summary: Per Customer/Cutoff</h3>
<h4 class="noppadding">For the Year <?php echo $_POST["selmonth"];?></h4>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px">
  <tr>
    <th>Classification</th>
    <th colspan="2">Customer</th>
    <th>UOM</th>
    <?php
    	for ($x=1; $x<=12; $x++){
			
			$monthNum  = $x;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('M');
			
			
			$a_date = $_POST["selmonth"]."-".$x."-01";
			
	?>
    <th class="text-center"><?php echo $monthName."<br>1-15"; ?></th>
  	<th class="text-center"><?php echo $monthName."<br>16-". date("t", strtotime($a_date)); ?></th>
    <?php } ?>
  </tr>
  
  <?php
 	$sql = "Select b.ccode, c.cname, b.dcutdate, sum(a.nprice*a.nqty) as namnt, c.ccustomerclass as cclass, d.cdesc
	From sales_t a
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
	left join groupings d on c.ccustomerclass=d.ccode and d.ctype='CUSTCLS'
	where a.compcode='$company' and YEAR(b.dcutdate) = '".$_POST["selmonth"]."' and b.lcancelled=0 and b.lapproved=1
	group by c.ccustomerclass, b.ccode, c.cname, b.dcutdate,d.cdesc
	order by c.ccustomerclass, c.cname, b.dcutdate";
 
 	echo $sql;
	
  ?>
  
</table>

</body>
</html>