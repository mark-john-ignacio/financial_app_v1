<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesOrders.php";

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
	<title>JO Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Job Order Summary: Per Item</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <tr>
  	<th>Item Class</th>
    <th colspan="2">Product</th>
    <th>UOM</th>
    <td align="right"><b>Total Qty</b></td>
  </tr>
  
<?php
	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$txtCustID = $_POST["txtCustID"];
	$itmtype = $_POST["seltype"];
	$itmclass = $_POST["seliclass"];
	$custype = $_POST["selcustype"];
	$trantype = $_POST["seltrantype"]; 
	$postedtran = $_POST["sleposted"];

	$mainqry = "";
	$finarray = array();

	$qryitm = "";
	if($txtCustID!=""){
		$qryitm = $qryitm." and b.ccode='".$txtCustID."'";
	}

	if($itmtype!=""){
		$qryitm = $qryitm." and c.ctype='".$itmtype."'";
	}

	if($itmclass!=""){
		$qryitm = $qryitm." and c.cclass='".$itmclass."'";
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

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From so_t a	
	left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
	order by  c.cclass, c.citemdesc");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntso_t a	
	left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
	order by c.cclass, c.citemdesc");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.cclass, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
	From (
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From so_t a	
	left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
	UNION ALL
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntso_t a	
	left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc) A Group By A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.cclass, A.typdesc order by A.cclass, A.citemdesc");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}


$ts1 = strtotime($date1);
$ts2 = strtotime($date2);

$year1 = date('Y', $ts1);
$year2 = date('Y', $ts2);

$month1 = date('m', $ts1);
$month2 = date('m', $ts2);

$mnths = (($year2 - $year1) * 12) + ($month2 - $month1);

$mnths = $mnths + 1;
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	foreach($finarray as $row)
	{
		
		if($class!=$row['cclass']){
			$classval=$row['typdesc'];
			$classcode="class='rpthead'";
		}
?>  
  <tr <?php echo $classcode;?> >
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo strtoupper($row['citemdesc']);?></td>
    <td><?php echo $row['cunit'];?></td>
    <td align="right"><?php echo number_format($row['nqty']);?></td>
  </tr>
<?php 
		$class=$row['cclass'];
		$classval="";
		$classcode="";

		//$totCost = $totCost + $row['ncost'];
		$totPrice = $totPrice + $row['nprice'];
	}
?>

    <!--<tr class='rptGrand'>
    	<td colspan="4" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
    	<td align="right">&nbsp;</td>
    	<td align="right">&nbsp;</td>
    	<td align="right"><b><?//php echo number_format($totPrice,2);?></b></td>
    </tr>-->
</table>
</body>
</html>

<script type="text/javascript">
$(document).ready(function() {

	//$('#MyTable tbody tr:last').clone().insertBefore('#MyTable tbody tr:first');
});
</script>