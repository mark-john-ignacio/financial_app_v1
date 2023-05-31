<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesPerCust.php";

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
<title>Sales Per Customer</title>
</head>

<body style="padding:10px">

<?php

$sql0 = "Select A.ccode, C.cname from sales A left join customers C on A.ccode=C.cempid and A.compcode=C.compcode where A.ccode in ('12566',
'12151',
'30029',
'30092',
'12309',
'11012',
'12646',
'30065',
'12155',
'12641',
'12554',
'30211',
'12791',
'12569',
'30009',
'12133',
'12255',
'12731',
'12603',
'12632',
'12173',
'12031',
'12094',
'12548',
'12541',
'50110',
'42135',
'12705',
'33046',
'12621',
'12760',
'12339',
'12803',
'30053',
'12202',
'30136',
'70076',
'12253',
'12754',
'50092',
'12764',
'12219',
'12668',
'12805',
'70040')";

$resultMAIN=mysqli_query($con,$sql0);

while($rowx = mysqli_fetch_array($resultMAIN, MYSQLI_ASSOC))
	{
?>



<div style="page-break-before:always">

<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Per Customer</h2>
<h3>For the Period January 01, 2019 to April 12, 2019</h3><br>
<h3><?php echo $rowx['ccode']." - ".$rowx['cname'];?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th>Date</th>
    <th>Invoice No.</th>
    <!--<th>Gross</th>-->
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th>Qty</th>
    <th>Price</th>
    <th>Amount</th>
  </tr>
  
<?php

$date1 = "2019-01-01";
$date2 = "2019-04-12";
$custid = $rowx['ccode'];
$cType = "Cripples";
//$cType = "Grocery";

$sql = "select A.dcutdate, A.ctranno as csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.namount
FROM(
select b.dcutdate, a.ctranno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
From sales_t a
left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
where a.compcode='$company' and b.ccode='".$rowx['ccode']."' and b.dcutdate between '2019-01-01' and '2019-04-12' and b.lcancelled=0 and d.ctype='$cType'
) A
order by A.dcutdate, A.ctranno";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$salesno = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$nGross=0;
	$cntr = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['csalesno']){
			$cntr = $cntr + 1;
			$invval = $row['csalesno'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
			//$nGross = $row['ngross'];
			
				if($cntr>1){
			?>
            
            <tr>
                <td colspan="7" align="right"><b>T O T A L:</b></td>
                <td align="right"><b><?php echo number_format($nGross,4);?></b></td>
            </tr>
           
            <?php
					$nGross = 0;
				}
			
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php echo $invval;?></td>
    <!--<td><?php //echo $nGross;?></td>-->
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo $row['nqty'];?></td>
    <td align="right"><?php echo number_format($row['nprice'],4);?></td>
    <td align="right"><?php echo number_format($row['namount'],4);?></td>
  </tr>
<?php 
		$invval = "";
		$dateval="";		
		$classcode="";
		$nGross = $nGross + $row['namount'];		
		$salesno=$row['csalesno'];
		$totAmount = $totAmount + $row['namount'];
	}
?>

            <tr>
                <td colspan="7" align="right"><b>T O T A L:</b></td>
                <td align="right"><b><?php echo number_format($nGross,4);?></b></td>
            </tr>

    <tr class='rptGrand'>
    	<td colspan="7" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo number_format($totAmount,4);?></b></td>
    </tr>
</table>

</div>

<?php
	}
?>
</body>
</html>