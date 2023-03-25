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
<title>Sales Per Customer</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Sales Detailed</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px">
  <tr>
    <th nowrap>Date</th>
    <th nowrap>Invoice No.</th>
    <th nowrap>Customer Type</th>
    <th nowrap>Customer Code</th>
    <th nowrap>Customer Name</th>
    <th nowrap colspan="2">Product</th>
    <th nowrap>UOM</th>
    <td nowrap align="right"><b>Qty</b></td>
    <td nowrap align="right"><b>Price</b></td>
		<td nowrap align="right"><b>Discount</b></td>
		<td nowrap align="right"><b>Net Price</b></td>
    <td nowrap align="right"><b>Amount</b></td>
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

	$result=mysqli_query($con,"select b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	order by a.ctranno, a.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	order by a.ctranno, a.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount, A.namount
	From (
		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."

		UNION ALL

		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
	) A 
	order by A.ctranno, A.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;
	foreach($finarray as $row)
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['ctranno']){
			$invval = $row['ctranno'];
			$remarks = $row['cname'];
			$ccode = $row['ccode'];
			$crank = $row['typdesc'];
		//	$ngross = $row['ngross'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}

		$netprice = floatval($row['nprice']) - floatval($row['ndiscount']);
		
?>  
  <tr <?php echo $classcode;?>>
    <td nowrap><?php echo $dateval;?></td>
    <td nowrap><?php echo $invval;?></td>
    <td nowrap><?php echo $crank;?></td>
    <td nowrap><?php echo $ccode;?></td>
    <td nowrap><?php echo $remarks;?></td>
    <td nowrap><?php echo $row['citemno'];?></td>
    <td nowrap><?php echo $row['citemdesc'];?></td>
    <td nowrap><?php echo $row['cunit'];?></td>
    <td nowrap align="right"><?php echo number_format($row['nqty']);?></td>
    <td nowrap align="right"><?php echo number_format($row['nprice'],2);?></td>
		<td nowrap align="right"><?=(floatval($row['ndiscount'])!==0.00) ? number_format($row['ndiscount'],2) : ""?></td>
		<td nowrap align="right"><?php echo number_format($netprice,2);?></td>
    <td nowrap align="right"><?php echo number_format($row['namount'],2);?></td>
  </tr>
<?php 
		$invval = "";
		$remarks = "";
		$dateval="";		
		$classcode="";		
		$ngross = "";
		$crank = "";
		$ccode = "";
		$salesno=$row['ctranno'];
		$totAmount = $totAmount + $row['namount'];
	}
?>

    <tr class='rptGrand'>
    	<td colspan="12" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo number_format($totAmount,2);?></b></td>
    </tr>
</table>

</body>
</html>