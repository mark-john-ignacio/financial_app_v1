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
<h2>Sales Summary: Per Customer Monthly</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>

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

		$sqlx = "select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, d.ccustomertype, e.cdesc
		order by d.ccustomertype, b.ccode, YEAR(b.dcutdate), MONTH(b.dcutdate)";

	}elseif($trantype=="Non-Trade"){

		$sqlx = "select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, d.ccustomertype, e.cdesc
		order by d.ccustomertype, b.ccode, YEAR(b.dcutdate), MONTH(b.dcutdate)";

	}else{

		$sqlx = "Select A.mdate, A.ydate, A.compcode, A.ccode, A.cname, A.ctype, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
		From (
			select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
			From sales_t a	
			left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
			Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, d.ccustomertype, e.cdesc
			UNION ALL
			select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
			From ntsales_t a	
			left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
			Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, d.ccustomertype, e.cdesc
		) A 
		Group By A.mdate, A.ydate, A.compcode, A.ccode, A.cname, A.ctype, A.typdesc 
		order by A.ctype, A.ccode, ydate, mdate";

	}

	//echo $sqlx;

	$mtnyr = array();
	$customers = array();

	$result=mysqli_query($con,$sqlx);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;

		$myrxc = $row['mdate']."/".$row['ydate'];
		if (!in_array($myrxc, $mtnyr)) {
			$mtnyr[] = $myrxc;
		}

		if (!in_array($row['ccode'], $customers)) {
			$customers[] = $row['ccode'];
		}

	}

	
	$sqlcustos = mysqli_query($con, "Select A.cempid, A.cname, A.ctradename, A.ccustomertype as ctype, B.cdesc as typdesc from customers A left join groupings B on A.ccustomertype=B.ccode and A.compcode=B.compcode and B.ctype='CUSTYP' where A.compcode='$company' and A.cempid in ('".implode("','",$customers)."') Order by A.ccustomertype, A.ctradename");

	asort($mtnyr);
?>
<table width="100%" border="1" align="center" id="MyTable" cellpadding="3">
  <tr>
  	<th>Customer Type</th>
    <th colspan="2">Customer</th>
		<?php
			$mnthltot = array();
			foreach($mtnyr as $xmnt){
				$mnth = explode("/",$xmnt);

				$mnthltot[$mnth[0].$mnth[1]] = 0;
		?>
			<th align="center" style="text-align: center !important"><?=date('F', mktime(0, 0, 0, $mnth[0], 10))."<br>".$mnth[1];?></th>
		<?php
			}
		?>
    <td align="right"><b>Total Amount</b></td>
  </tr>
  
<?php	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	
	while($row = mysqli_fetch_array($sqlcustos, MYSQLI_ASSOC))
	{
		$cxtotal = 0;

		if($class != TRIM($row['ctype'])){

			$classval=$row['typdesc'];
			$classcode="class='rpthead'";
		}
?>  
  <tr <?php echo $classcode;?> >
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['cempid'];?></td>
    <td><?php echo $row['ctradename'];?></td>
    <?php
			foreach($mtnyr as $rs8){
				$mnth = explode("/",$rs8);

				$nprx = 0;
				foreach($finarray as $rs9){
					if($row['cempid']==$rs9['ccode'] && $mnth[0]==$rs9['mdate'] && $mnth[1]==$rs9['ydate']) {
						$nprx = $rs9['nprice'];
					}
				}
		?>
			<td style="text-align: right !important"><?=floatval($nprx!=0) ? number_format($nprx,2) : "";?></td>
		<?php
				$cxtotal = $cxtotal + $nprx;
				$mnthltot[$mnth[0].$mnth[1]] = $mnthltot[$mnth[0].$mnth[1]] + $nprx;
			}
		?>

		<td style="text-align: right !important"><b><?=floatval($cxtotal!=0) ? number_format($cxtotal,2) : "";?></b></td>

  </tr>
<?php 
	$class = TRIM($row['ctype']);
	$classval = "";
	$classcode = "";

		//$totCost = $totCost + $row['ncost'];
		//$totPrice = $totPrice + $row['nprice'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="3" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
			<?php
			$totPrice = 0;
				foreach($mtnyr as $rs8){
					$mnth = explode("/",$rs8);
			?>
				<td align="right"><b><?=number_format($mnthltot[$mnth[0].$mnth[1]],2)?></b></td>
			<?php
					$totPrice = $totPrice + floatval($mnthltot[$mnth[0].$mnth[1]]);
				}
			?>
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