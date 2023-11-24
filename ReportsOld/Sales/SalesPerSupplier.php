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
<title>Sales Per Supplier</title>

<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Per Supplier</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
<h3><?php echo $_POST["txtCust"];?></h3>
</center>
<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <thead>
  <tr>
    <th>Date</th>
    <th>Invoice No.</th>
    <th colspan="2">Customer</th>
    <th colspan="2">Product</th>
    <th>UOM</th>
    <th>Qty</th>
    <th>S-Price</th>
    <th>Price</th>
    <th>Amount</th>
  </tr>
  </thead>
  
  <tbody>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$custid = $_POST["txtCustID"];
//$rpt = $_POST["selrpt"];

$sql = "select b.dcutdate, a.ctranno as csalesno, b.ccode, f.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross
From sales_t a
left join sales b on a.ctranno=b.ctranno
left join 
	(
		Select distinct X.citemno, Y.ccode
		from receive_t X left join receive Y on X.ctranno=Y.ctranno
		where Y.ccode='$custid'
	) C on A.citemno=C.citemno
left join suppliers e on c.ccode=e.ccode
left join customers f on b.ccode=f.cempid
left join items d on a.citemno=d.cpartno
where a.compcode='001' and c.ccode='$custid' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
order by b.dcutdate, a.ctranno, a.nident";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$totCostAmount = 0;
	$ngross = 0;
	$cntr = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['csalesno']){
			$invval = $row['csalesno'];
			$remarks = $row['cremarks'];
			$ngross = $row['ngross'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?> id="tr<?php echo $cntr;?>">
    <td><?php echo $dateval;?><input type="hidden" name="hdndte" id="hdndte" value="<?php echo $row['dcutdate'];?>"></td>
    <td><?php echo $invval;?></td>
    <td><?php echo $row['ccode'];?><input type="hidden" name="hdnccode" id="hdnccode" value="<?php echo $row['ccode'];?>"></td>
    <td><?php echo $row['cname'];?></td>
    <td><?php echo $row['citemno'];?><input type="hidden" name="hdncode" id="hdncode" value="<?php echo $row['citemno'];?>"></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td><?php echo $row['cunit'];?><input type="hidden" name="hdnuom" id="hdnuom" value="<?php echo $row['cunit'];?>"></td>
    <td align="right"><?php echo $row['nqty'];?></td>
    <td align="right" id="td<?php echo $cntr."8";?>">0.00</td>
    <td align="right"><?php echo $row['nprice'];?></td>
    <td align="right"><?php echo $row['namount'];?></td>
  </tr>
<?php 
	$cntr = $cntr + 1;
	
		$invval = "";
		$remarks = "";
		$dateval="";		
		$classcode="";		
		$ngross = "";
		$salesno=$row['csalesno'];
		$totAmount = $totAmount + $row['namount'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="10" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo $totAmount;?></b></td>
    </tr>
    </tbody>
</table>

</body>
</html>

<script>
$(document).ready(function() {
	
	$("#MyTable > tbody > tr").each(function(index) {	
			
				var xcitm = $(this).find('input[type="hidden"][name="hdncode"]').val();
				var xcuom = $(this).find('input[type="hidden"][name="hdnuom"]').val();
				var xddte = $(this).find('input[type="hidden"][name="hdndte"]').val();
				var ccode = $(this).find('input[type="hidden"][name="hdnccode"]').val();
				
				//alert("th_checkitmprice.php?itm="+xcitm+"&cust="+ccode+"&cunit="+xcuom+"&dte="+xddte)	;
				$.ajax ({
					url: "../th_checkitmprice.php",
					data: { itm: xcitm, cust: ccode, cunit: xcuom, dte: xddte},
					async: false,
					success: function( data ) {
						//alert(data);
						 $("#td"+index+"8").html(data);
						// $(this).find("td:contains('0.00')").html(data);
						 //$('#MyTable').find('').find('td:eq(8)').html(data);
						//alert($(this).find("td:eq(1)").text() +" : "+ data) ;


							//$(this).find('td:eq(9)').html(data);
					}
				});
			
	});
	
});
</script>