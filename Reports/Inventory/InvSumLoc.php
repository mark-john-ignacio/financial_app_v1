<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvLoc";


	ini_set('MAX_EXECUTION_TIME', 900);

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];
	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}


	$dtfrom = $_POST["date1"];

	$arrOut = array();
	$sqlOUT = "select A.dcutdate, A.tblinvin_nidentity, A.citemno, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, SUM(A.ntotqty) as nqty
	from tblinvout A 
	where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$dtfrom', '%m/%d/%Y')
	Group BY A.dcutdate, tblinvin_nidentity, A.citemno, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation";
	$rsdout = mysqli_query($con,$sqlOUT);
	while($row = mysqli_fetch_array($rsdout, MYSQLI_ASSOC)){
		$arrOut[] = $row;
	}

	$sqlitm = "select A.nidentity, A.dcutdate, A.citemno, C.citemdesc, A.cmainunit, A.cjono, A.csupplier, A.clotsno, A.cpacklist, A.nlocation, B.cdesc as locs_desc, SUM(A.ntotqty) as nqty
	from tblinvin A 
	left join mrp_locations B on A.compcode=B.compcode and A.nlocation=B.nid
	left join items C on A.compcode=C.compcode and A.citemno=C.cpartno
	where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$dtfrom', '%m/%d/%Y')
	Group BY A.nidentity, A.dcutdate, A.citemno, C.citemdesc, A.cmainunit, A.clotsno, A.cpacklist, A.nlocation, B.cdesc
	Order by A.dcutdate ASC";
	
	$arrIN = array();
	$rsditm = mysqli_query($con,$sqlitm);
	if(mysqli_num_rows($rsditm)>=1){
		
		while($row = mysqli_fetch_array($rsditm, MYSQLI_ASSOC)){
			$arrIN[] = $row;
		}
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
  <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Inventory Summary</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?php echo strtoupper($compname);  ?></h2>
<h3 class="nopadding">Inventory Summary Per Location</h3>
<h4 class="nopadding">As Of <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?></h4>
</center>
<br>

<table width="100%" border="0" align="center" cellpadding="3">
  <tr>
    <th style="text-align:center;">Item Code</th>
	<th style="text-align:center;">Part Number</th>
    <th style="text-align:center;">LOT #</th>
	<th style="text-align:center;">JO #</th>
	<th style="text-align:center;">Packing List</th>
	<th style="text-align:center;">Suppliers Name</th>
	<th style="text-align:center;">Receive Date</th>
	<th style="text-align:center;">Location</th>
	<th style="text-align:center;">UOM</th>
	<th style="text-align:right;">Balance</th>
  </tr>
  <?php
  	$qtyremain = 0;
	foreach($arrIN as $row){
		$qtyremain = floatval($row['nqty']);
		
		foreach($arrOut as $rout){
			if($row['nidentity']==$rout['tblinvin_nidentity']){
				$qtyremain = floatval($row['nqty']) - floatval($rout['nqty']);
			}
		}

		if(floatval($qtyremain)>0){
	?>
		<tr>
			<td style="text-align:center;"><?=$row['citemno']?></td>
			<td style="text-align:center;"><?=$row['citemdesc']?></td>
			<td style="text-align:center;"><?=$row['clotsno']?></td>
			<td style="text-align:center;"><?=$row['cjono']?></td>
			<td style="text-align:center;"><?=$row['cpacklist']?></td>
			<td style="text-align:center;"><?=$row['csupplier']?></td>
			<td style="text-align:center;"><?=$row['dcutdate']?></td>
			<td style="text-align:center;"><?=$row['locs_desc']?></td> 
			<td style="text-align:center;"><?=$row['cmainunit']?></td> 
			<td style="text-align:right;"><?=number_format($qtyremain,2)?></td>
		</tr>
	<?php
		}
	}
  ?>
</table>

</body>
</html>

