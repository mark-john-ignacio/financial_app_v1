<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "TBal.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$company = $_SESSION['companyid'];

	$arrcomps = array();
	$arrcompsname = array();
	$arrcompsids = array();
	$sql = "select compcode, compname From company";
	$result=mysqli_query($con,$sql);
  	$companycnt=mysqli_num_rows($result);
	if($companycnt>0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrcomps[] = $row;
			$arrcompsname[] = $row['compname'];
			$arrcompsids[] = $row['compcode'];
		}
	}

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css?x=<?=time()?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Trial Balance</title>

	<style>
		tr:hover {
			background-color: gainsboro;
		}
		@media print {
			.my-table {
				width: 100% !important;
			}
		}
	</style>
</head>

<body style="padding:10px">
<h2>Company: <?=implode(", ",$arrcompsname);  ?></h2>
<h2><b>Trial Balance (Consolidated)</b></h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>


<br><br>
<table width="100%" border="0" align="center" class="my-table">
  <tr>
    <th rowspan="2" style="text-align:center" width="100px">Account No. </th>
    <th rowspan="2" style="text-align:center">Account Title</th>
    <?php
		$ntotdebit = array();
		$ntotcredit = array();

		foreach($arrcomps as $row){

			$ntotdebit[$row['compcode']] = 0;
			$ntotcredit[$row['compcode']] = 0;
	?>
		<th colspan="3"  style="text-align:center; border-right: 1px solid"> <?=$row['compname']?></th>
	<?php
		}
	?>

		<th colspan="3"  style="text-align:center"> Grand Total </th>
  </tr>
  <tr>
	<?php
		foreach($arrcomps as $row){
	?>
		<th style="text-align:center"  width="150px">Debit</th>
		<th style="text-align:center"  width="150px">Credit</th>
		<th style="text-align:center; border-right: 1px solid; padding-right: 20px"  width="150px">Balance</th>
	<?php
		}
	?>
		<th style="text-align:center"  width="150px">Debit</th>
		<th style="text-align:center"  width="150px">Credit</th>
		<th style="text-align:center"  width="150px">Balance</th>
  </tr>
 
 <?php

	$qrytotdebit = array();
	$qrytotcredit = array();
	$qry_accts = array();
	$qry_acctsnames = array();

	$sql = "Select A.compcode, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode in ('".implode("','", $arrcompsids)."') and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
			Group By A.compcode, A.acctno, B.cacctdesc
			Order By A.compcode, A.acctno";

		//	echo $sql;

	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$qry_accts[] = $row['acctno'];
		$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];

		$qrytotdebit[$row['compcode']][$row['acctno']] = $row['ndebit'];
		$qrytotcredit[$row['compcode']][$row['acctno']] = $row['ncredit'];
	}

	$hdr_accts = array_unique($qry_accts);
	asort($hdr_accts);

	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;
	
	$xdrrowtot = 0;
	$xcrrowtot = 0;
	$xtotalrow = 0;

	$Gxdrrowtot = 0;
	$Gxcrrowtot = 0;
	$Gxtotalrow = 0;

	foreach($hdr_accts as $rz){
?>
<tr>
    <td onclick="funcset('<?=$rz?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer " nowrap><?php echo $rz;?></td>
    <td onclick="funcset('<?=$rz?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer " nowrap><?php echo $qry_acctsnames[$rz];?></td>

	<?php
		foreach($arrcomps as $row){

			if(isset($qrytotdebit[$row['compcode']][$rz])){
				$ndramt = $qrytotdebit[$row['compcode']][$rz];
			}else{
				$ndramt = 0;
			}

			if(isset($qrytotcredit[$row['compcode']][$rz])){
				$ncramt = $qrytotcredit[$row['compcode']][$rz];
			}else{
				$ncramt = 0;
			}

			$ntotdebit[$row['compcode']] = $ntotdebit[$row['compcode']] + floatval($ndramt);
			$ntotcredit[$row['compcode']] = $ntotcredit[$row['compcode']] + floatval($ncramt);

			$ntotbal = floatval($ndramt) - floatval($ncramt);

			$xdrrowtot += floatval($ndramt);
			$xcrrowtot += floatval($ncramt);
	
?>
  
  	<td style="text-align:right"><?php if (floatval($ndramt)<>0) { echo number_format(floatval($ndramt), 2); }?></td>
    <td style="text-align:right"><?php if (floatval($ncramt)<>0) { echo number_format(floatval($ncramt), 2); }?></td>
	<td style="text-align:right; border-right: 1px solid; padding-right: 20px"><?=$ntotbal < 0 ? "(".number_format(abs($ntotbal),2).")" : number_format($ntotbal,2) ?></td>
<?php
		}

		$xtotalrow = floatval($xdrrowtot) - floatval($xcrrowtot);
?>

	<td style="text-align:right"><?php if (floatval($xdrrowtot)<>0) { echo number_format(floatval($xdrrowtot), 2); }?></td>
    <td style="text-align:right"><?php if (floatval($xcrrowtot)<>0) { echo number_format(floatval($xcrrowtot), 2); }?></td>
	<td style="text-align:right"><?=$xtotalrow < 0 ? "(".number_format(abs($xtotalrow),2).")" : number_format($xtotalrow,2) ?></td>
</tr>
<?php
		$Gxdrrowtot += floatval($xdrrowtot);
		$Gxcrrowtot += floatval($xcrrowtot);

		$xdrrowtot = 0;
		$xcrrowtot = 0;
		$xtotalrow = 0;
	}
?>
 
    <tr>
		<td colspan="2"><b>TOTALS: </b></th>
		<?php
			foreach($arrcomps as $row){

				$ntotGBal = floatval($ntotdebit[$row['compcode']]) - floatval($ntotcredit[$row['compcode']]);
		?>
		<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotdebit[$row['compcode']]), 2);?></b></th>
		<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotcredit[$row['compcode']]), 2);?></b></th>
		<td  style="text-align:right; border-top:1px solid; border-bottom:5px double; border-right: 1px solid; padding-right: 20px"><b>
		<?=$ntotGBal < 0 ? "(".number_format(abs($ntotGBal),2).")" : number_format($ntotGBal,2) ?></b></th>
		<?php
			}

			$Gxtotalrow = floatval($Gxdrrowtot) - floatval($Gxcrrowtot);
		?>

		<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($Gxdrrowtot), 2);?></b></th>
		<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($Gxcrrowtot), 2);?></b></th>
		<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b>
		<?=$Gxtotalrow < 0 ? "(".number_format(abs($Gxtotalrow),2).")" : number_format($Gxtotalrow,2) ?></b></th>

	</tr>
 
</table>



<?php

		$sql = "Select A.cmodule, A.ctranno, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where A.compcode='$company' and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
		Group By A.cmodule, A.ctranno Order By A.cmodule, A.ctranno";

		$result=mysqli_query($con,$sql);
		$arrlist = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$xsum = floatval($row['ndebit']) - floatval($row['ncredit']);
			if(abs($xsum) > 1) {
				$arrlist[] = $row;
			}
			
		}

	if(count($arrlist) >= 1){
?>
<br><br>
<h3>Check for unbalance transactions</h3>

<table width="50%" border="0" align="left" class="my-table">
	<tr>	
		<th> Module </th>
		<th> Transaction No. </th>
		<th style='text-align: right'> Total Debit </th>
		<th style='text-align: right'> Total Credit </th>
		<th style='text-align: right'> Unbalance </th>
	</tr>
	<?php
		foreach($arrlist as $rs){
			$xsum = floatval($rs['ndebit']) - floatval($rs['ncredit']);
	?>
	<tr>	
		<td> <?=$rs['cmodule']?> </td>
		<td> <?=$rs['ctranno']?> </td>
		<td align="right"> <?=number_format($rs['ndebit'],2)?> </td>
		<td align="right"> <?=number_format($rs['ncredit'],2)?> </td>
		<td align="right"> <?=number_format(abs($xsum),2)?> </td>
	</tr>
	<?php
		}
	?>
</table>
<br><br>
<?php
	}
?>

<form action="TBal_Det.php" name="frmdet" id="frmdet" target="_blank" method="POST">
	<input type="hidden" name="ccode" id="ccode" value="">
	<input type="hidden" name="date1" id="date1" value="">
	<input type="hidden" name="date2" id="date2" value="">
</form>

</body>
</html>

<script>
	function funcset(xcode, xdte1, xdte2){

		document.getElementById("ccode").value = xcode;
		document.getElementById("date1").value = xdte1;
		document.getElementById("date2").value = xdte2;
	
		document.getElementById("frmdet").submit(); 
	}
</script>