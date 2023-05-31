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
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sales Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Summary: Per Item</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <tr>
  	<th>Item Type</th>
    <th colspan="2">Product</th>
    <th>UOM</th>
    <td align="right"><b>Ave. Sales / Month</b></td>
    <td align="right"><b>Qty</b></td>
    <td align="right"><b>Total Amount</b></td>
  </tr>
  
<?php
$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$itmtype = $_POST["seltype"];
$custype = $_POST["selcustype"];
$trantype = $_POST["seltrantype"]; 
$postedtran = $_POST["sleposted"];

$mainqry = "";
$finarray = array();

$qryitm = "";
if($itmtype!==""){
	$qryitm = " and c.ctype='".$itmtype."'";
}

$qrycust = "";
if($custype!==""){
	$qrycust = " and d.ccustomertype='".$custype."'";
}

$qryposted = "";
if($postedtran!==""){
	$qryposted = " and b.lapproved=".$postedtran."";
}

if($trantype=="Trade"){

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc
	order by sum(A.nprice*a.nqty) DESC");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc
	order by sum(A.nprice*a.nqty) DESC");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.ctype, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
	From (
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc
	UNION ALL
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc) A Group By A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.ctype, A.typdesc order by sum(A.nprice) DESC");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}

$mnths = (int)abs((strtotime($date1) - strtotime($date2))/(60*60*24*30)) + 1;
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	foreach($finarray as $row)
	{
		
		if($class!=$row['ctype']){
			$classval=$row['typdesc'];
			$classcode="class='rpthead'";
		}
?>  
  <tr <?php echo $classcode;?> >
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo strtoupper($row['citemdesc']);?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo floatval($row['nqty']) / $mnths;?></td>
    <td align="right"><?php echo number_format($row['nqty']);?></td>
    <td align="right"><?php echo number_format($row['nprice'],2);?></td>
  </tr>
<?php 
$class=$row['ctype'];
$classval="";
$classcode="";

		//$totCost = $totCost + $row['ncost'];
		$totPrice = $totPrice + $row['nprice'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="4" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right">&nbsp;</td>
    	<td align="right">&nbsp;</td>
    	<td align="right"><b><?php echo number_format($totPrice,2);?></b></td>
    </tr>
</table>
</body>
</html>

<script type="text/javascript">
$( document ).ready(function() {

	$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
});
</script>