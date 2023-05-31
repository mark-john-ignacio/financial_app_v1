<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvSum.php";

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
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
function getcost(itm,dte){
	//alert(itm);
		$.ajax ({
			url: "th_ItemCost.php",
			data: { id: itm, dte: dte },
			async: false,
			dataType: "text",
			success: function( data ) {
				var y = data.trim();
				var x = y.split(":",2);
				document.write("<td align=\"right\" nme=\"itmcost\">"+x[0]+"</td><td align=\"right\" nme=\"itmret\">"+x[1]+"</td>");				
											 
			}
		});

}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sales Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Inventory Summary</h2>
<h3>As Of <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
  	<th>Classification</th>
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th>Qty</th>
    <th>Total Cost</th>
    <th>Total Retail</th>
  </tr>
  
<?php

$date1 = date_format(date_create($_POST["date1"]),"Y-m-d");

$sql = "select d.cclass, c.cdesc, a.citemno, d.citemdesc, a.cunit, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty, 0 as nprice, 0 as ncost
From tblinventory a
right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
left join groupings c on d.cclass=c.ccode and d.compcode=c.compcode and c.ctype='ITEMCLS'
where a.compcode='$company' and a.dcutdate <= '$date1'
group by d.cclass, c.cdesc,a.citemno, d.citemdesc, a.cunit
order by d.cclass, d.citemdesc";
//echo $sql;
$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($class!=$row['cclass']){
			$classval=$row['cdesc'];
			$classcode="class='rpthead'";
		}
	$nretprice = $row['nqty'] * $row['nprice'];
	$ncostprice= $row['nqty'] * $row['ncost'];
	
	if($row['nqty'] <> 0){
?>  
  <tr <?php echo $classcode;?>>
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo $row['nqty'];?></td>
  			<?php
				if((float)$row['nqty'] >= 1){
			?>
  
        <script>
			
			getcost('<?php echo $row['citemno']; ?>','<?php echo $_POST["date1"]; ?>');
		</script>
			 	<?php
			}else{
				echo "<td>&nbsp;</td><td>&nbsp;</td>";
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
	
	}
?>

    <tr class='rptGrand'>
    	<td colspan="4" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right">&nbsp;</td>
    	<td align="right"><b><?php echo $totPrice;?></b></td>
        <td align="right"><b><?php echo $totCost;?></b></td>
    </tr>
</table>

</body>
</html>

