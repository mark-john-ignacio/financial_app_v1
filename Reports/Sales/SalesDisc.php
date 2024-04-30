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
<title>SO vs DR vs SI</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>SO vs DR vs SI</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px">
	<thead>
		<tr>
			<th nowrap>Delivery Date</th>
			<th nowrap>SO No.</th>
			<th nowrap>Customer</th>
			<th nowrap>Item Code</th>
			<th nowrap>Item Desc</th>
			<th nowrap>UOM</th>
			<td nowrap align="center"><b>SO Qty</b></td>
			<td nowrap align="center"><b>DR Qty</b></td>
			<td nowrap align="center"><b>SI Price</b></td>
			<td nowrap align="center"><b>SI Qty</b></td>
			<td nowrap align="center"><b>Returned</b></td>
		</tr>
	</thead>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$custype = $_POST["selcustype"];
$trantype = $_POST["seltrantype"]; 
$postedtran = $_POST["sleposted"];

$mainqry = "";
$finarray = array();

$qrycust = "";
if($custype!==""){
	$qrycust = " and d.ccustomertype='".$custype."'";
}

$qryposted = "";
if($postedtran!==""){
	$qryposted = " and b.lapproved=".$postedtran."";
}


//if($trantype!==""){
	$xsql = "select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
	From so_t a	
	left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and b.lvoid=0
	".$qryposted.$qrycust."
	order by a.ctranno, a.nident";


/*}else{
	$xsql = "Select A.nident, A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.namount
	From (
		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
		From so_t a	
		left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and b.lvoid=0
		".$qryposted.$qrycust."

		UNION ALL

		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
		From ntso_t a	
		left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and b.lvoid=0
		".$qryposted.$qrycust."
	) A 
	order by A.ctranno, A.nident";
	
}*/

$resDR=mysqli_query($con,"Select A.ctranno, A.nident, A.creference, A.crefident, A.citemno, A.nqty from dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0");
$findr = array();
while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
	$findr[] = $row;
}

$resSI=mysqli_query($con,"Select creference, nrefident, citemno, A.nprice, sum(nqty) as nqty, sum(A.nqtyreturned) as nqtysr from sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 Group By creference, nrefident, citemno, A.nprice");
$finsi = array();
while($row = mysqli_fetch_array($resSI, MYSQLI_ASSOC)){
	$finsi[] = $row;
}

//echo $xsql;

$result=mysqli_query($con,$xsql);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$finarray[] = $row;
}

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	foreach($finarray as $row)
	{

		$netprice = 0;

		$edrqty = 0;
		$drnos = array();
		$dridents = array();
		foreach($findr as $drow){
			if($drow['creference']==$row['ctranno'] && $drow['citemno']==$row['citemno'] && $drow['crefident']==$row['nident']){
				$edrqty = $edrqty + floatval($drow['nqty']);
				$drnos[] = $drow['ctranno'];
				$dridents[] = $drow['nident'];
			}
		}

		$esiqty = 0;
		$esiqtyret = 0;
		$esiprice = 0;
		foreach($finsi as $srow){
			if(in_array($srow['creference'], $drnos) && in_array($srow['nrefident'], $dridents) && $srow['citemno']==$row['citemno']){
				$esiqty = $esiqty + floatval($srow['nqty']);
				$esiqtyret = $esiqtyret + floatval($srow['nqtysr']);
				$esiprice = $srow['nprice'];
				break;
			}
		}

		if($salesno!=$row['ctranno']){
			$invval = $row['ctranno'];
			$remarks = $row['cname'];
			$ccode = $row['ccode'];
			$crank = $row['typdesc'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td nowrap><?php echo $dateval;?></td>
    <td nowrap><?php echo $invval;?></td>
    <td nowrap><?php echo $remarks;?></td>
    <td nowrap><?php echo $row['citemno'];?></td>
    <td nowrap><?php echo $row['citemdesc'];?></td>
    <td nowrap><?php echo $row['cunit'];?></td>
    <td nowrap align="right"><?php echo number_format($row['nqty']);?></td>
    <td nowrap align="right"><?php echo number_format($edrqty);?></td>
		<td nowrap align="right"><?php echo number_format($esiprice,2);?></td>
		<td nowrap align="right"><?php echo number_format($esiqty);?></td> 
		<td nowrap align="right"><?php echo number_format($esiqtyret);?></td>
  </tr> 
<?php 
		$salesno = "";
		$remarks = "";
		$invval = "";
		$code = "";
		$name= "";
		$dateval="";
		$classcode="";

		$salesno=$row['ctranno'];
	}
?>


</table>

</body>
</html>