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
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Per Customer</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
<h3><?php echo $_POST["txtCust"];?></h3>
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

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$custid = $_POST["txtCustID"];
$cType = $_POST["seltype"];
//$cType = "Grocery";

$sql = "select A.dcutdate, A.ctranno as csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.namount
FROM(
select b.dcutdate, a.ctranno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
From sales_t a
left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
where a.compcode='$company' and b.ccode='$custid' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and d.ctype='$cType'
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

</body>
</html>