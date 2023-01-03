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
<title>Sales Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Summary: Per Cutomer</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
  	<th>Classification</th>
    <th colspan="2">Customer</th>
    <th width="100">Total Grocery</th>
    <th  width="100">Total Cripples</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

//$sql = "select b.ccustomerclass as cclass, a.ccode, b.cname, sum(a.ngross) as namnt, c.cdesc
//From sales a
//left join customers b on a.ccode=b.cempid
//left join groupings c on b.ccustomerclass=c.ccode and c.ctype='CUSTCLS'
//where a.compcode='001' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lcancelled=0 and a.lapproved=1
//group by b.ccustomerclass, a.ccode, b.cname, c.cdesc
//order by b.ccustomerclass, b.cname";

$sql = "select A.cclass, A.ccode, A.cname, A.cdesc, sum(A.ngroc) as ngroc, sum(A.ncrip) as ncrip
from (
select b.ccustomerclass as cclass, a.ccode, b.cname, c.cdesc, 
case when d.ctype='GROCERY' then sum(x.namount) end as ngroc,
case when d.ctype='CRIPPLES' then sum(x.namount) end as ncrip
From sales_t x
left join sales a on x.ctranno=a.ctranno
left join customers b on a.ccode=b.cempid
left join groupings c on b.ccustomerclass=c.ccode and c.ctype='CUSTCLS'
left join items d on x.citemno=d.cpartno
where a.compcode='001' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lcancelled=0 and a.lapproved=1
group by b.ccustomerclass, a.ccode, b.cname, c.cdesc, d.ctype
    ) A
group by A.cclass, A.ccode, A.cname, A.cdesc
order by A.cclass, A.cname";
//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classdesc="";
	$classval="";
	$classcode="";
	$totPriceG=0;	
	$totPriceC = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($class!=$row['cclass']){
			$classval=$row['cclass'];
			$classdesc=$row['cdesc'];
			$classcode="class='rpthead'";
		}
?>  
  <tr <?php echo $classcode;?>>
    <td><b><?php echo $classdesc;?></b></td>
    <td><?php echo $row['ccode'];?></td>
    <td><?php echo utf8_encode($row['cname']);?></td>
    <td align="right"><?php echo $row['ngroc'];?></td>
    <td align="right"><?php echo $row['ncrip'];?></td>
  </tr>
<?php 
$class=$row['cclass'];
$classval="";
$classdesc="";
$classcode="";

		$totPriceG = $totPriceG + $row['ngroc'];
		$totPriceC = $totPriceC + $row['ncrip'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="3" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right"><b><?php echo $totPriceG;?></b></td>
        <td align="right"><b><?php echo $totPriceC;?></b></td>
    </tr>
</table>

</body>
</html>