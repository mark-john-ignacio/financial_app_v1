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
<h2>Sales Summary: Per Customer</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" id="MyTable">
  <tr>
  	<th>Customer Type</th>
    <th colspan="2">Customer</th>
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

	$sqlx = "select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
	order by d.ccustomertype, sum(A.namount) DESC";

}elseif($trantype=="Non-Trade"){

	$sqlx = "select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
	order by d.ccustomertype, sum(A.namount) DESC";

}else{

	$sqlx = "Select A.compcode, A.ccode, A.cname, A.lapproved, A.ctype, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
	From (
		select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0  and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		UNION ALL
		select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0  and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
	) A 
	Group By A.compcode, A.ccode, A.cname, A.lapproved, A.ctype, A.typdesc order by A.ctype, sum(A.nprice) DESC";

}

	$result=mysqli_query($con,$sqlx);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	foreach($finarray as $row)
	{
		
		if($class != TRIM($row['ctype'])){

			$classval=$row['typdesc'];
			$classcode="class='rpthead'";
		}
?>  
  <tr <?php echo $classcode;?> >
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['ccode'];?></td>
    <td><?php echo $row['cname'];?></td>
    <td align="right"><?php echo number_format($row['nprice'],2);?></td>
  </tr>
<?php 
	$class = TRIM($row['ctype']);
	$classval = "";
	$classcode = "";

		//$totCost = $totCost + $row['ncost'];
		$totPrice = $totPrice + $row['nprice'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="3" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
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