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
<h2>Sales Summary: Per Transaction</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
  	<th>Classification</th>
  	<th>Transaction No.</th>
  	<th>Date</th>
    <th colspan="2">Customer</th>
    <th>Total Amount</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$rpt = $_POST["seltype"];

if($rpt==""){
	$qrytyp = "";
}else{
	$qrytyp = " and d.ctype='$rpt'";
}


//$sql = "select b.ccustomerclass as cclass, a.ccode, b.cname, a.ngross as namnt, a.ctranno as csalesno, a.dcutdate, c.cdesc
//From sales a
//left join customers b on a.ccode=b.cempid
//left join groupings c on b.ccustomerclass=c.ccode and c.ctype='CUSTCLS'
//where a.compcode='001' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lcancelled=0 and a.lapproved=1
//order by b.ccustomerclass, b.cname, a.dcutdate";

$sql = "select b.ccustomerclass as cclass, a.ccode, b.cname, sum(x.namount) as namnt, a.ctranno as csalesno, a.dcutdate, c.cdesc
From sales_t x
left join sales a on x.compcode=a.compcode and x.ctranno = a.ctranno
left join customers b on a.compcode=b.compcode and a.ccode=b.cempid
left join groupings c on b.compcode=c.compcode and b.ccustomerclass=c.ccode and c.ctype='CUSTCLS'
left join items d on x.compcode=d.compcode and x.citemno=d.cpartno
where a.compcode='001' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') 
and a.lcancelled=0 and a.lapproved=1 ".$qrytyp."
group by b.ccustomerclass, a.ccode, b.cname, a.ctranno, a.dcutdate, c.cdesc
order by b.ccustomerclass, b.cname, a.dcutdate";

//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classval="";
	$classdesc="";
	$classcode="";
	$totPrice=0;	
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
    <td><?php echo $row['csalesno'];?></td>
    <td><?php echo $row['dcutdate'];?></td>
    <td><?php echo $row['ccode'];?></td>
    <td><?php echo utf8_encode($row['cname']);?></td>
    <td align="right"><?php echo $row['namnt'];?></td>
  </tr>
<?php 
$class=$row['cclass'];
$classval="";
$classdesc="";
$classcode="";

		$totPrice = $totPrice + $row['namnt'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="5" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right"><b><?php echo $totPrice;?></b></td>
    </tr>
</table>

</body>
</html>