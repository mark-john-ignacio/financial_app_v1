<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "TBal.php";

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

	$dteyr = $_POST["selyr"];

	$sql = "Select MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
		Group By MONTH(ddate), A.acctno, B.cacctdesc
		Order By A.acctno, MONTH(ddate)";

		//echo $sql;

	$result=mysqli_query($con,$sql);
	//if (!mysqli_query($con, $sql)) {
	//	printf("Errormessage: %s\n", mysqli_error($con));
	//} 

	$qry_accts = array();
	$qry_acctsnames = array();
	$months = array();

	$qrytotdebit = array();
	$qrytotcredit = array();

	$qryrows = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$qry_accts[] = $row['acctno'];
		$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];
		$months[] = $row['dmonth'];


		$qrytotdebit[$row['dmonth']][$row['acctno']] = $row['ndebit'];
		$qrytotcredit[$row['dmonth']][$row['acctno']] = $row['ncredit'];

		$qryrows[] = $row;
	}


	//For Beg Bal
	$dteyrminus = $dteyr - 1;
	$begtotdebit = array();
	$begtotcredit = array();

	$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
	From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
	where A.compcode='$company' and YEAR(A.ddate) = '$dteyrminus' and IFNULL(B.cacctdesc,'') <> ''
	Group By A.acctno, B.cacctdesc
	Order By A.acctno";

	$result=mysqli_query($con,$sql);
	$qryrows = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if(!in_array($row['acctno'], $qry_accts)){
			$qry_accts[] = $row['acctno'];
			$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];
		}

		$begtotdebit[$row['acctno']] = $row['ndebit'];
		$begtotcredit[$row['acctno']] = $row['ncredit'];

	}

	$hdr_months = array_unique($months);
	asort($hdr_months);

	$hdr_accts = array_unique($qry_accts);
	asort($hdr_accts);


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
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Trial Balance - Monthly</h2>
<h3>For the Year <?=$dteyr?></h3>
</center>

<br><br>
<table border="0" align="center" class="my-table">
  <tr>
    <th rowspan="2" width="50px">&nbsp;</th>
    <th rowspan="2" style="text-align:center" width="100px">Account No. </th>
    <th rowspan="2" style="text-align:center">Account Name</th>
	<th colspan="3" style="text-align:center">Beginning Balance (<?=$dteyrminus?>)</th>
		<?php
		$GtotDr = array();
		$GtotCr = array();

			foreach($hdr_months as $rx){
				$monthNum  = $rx;
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F');

				$GtotDr[$rx] = 0;
				$GtotCr[$rx] = 0;
		?>
    <th colspan="3"  style="text-align:center"><?=$monthName?></th>		
		<?php
			}
		?>
	<th colspan="3" style="text-align:center"> Grand Total </th>
  </tr>
  <tr>
  		<th style="text-align:center;  padding-right: 20px" width="150px">Debit</th>
		<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
		<th style="text-align:center;  padding-right: 20px" width="150px">Balance</th>

		<?php
				$cnts = 0;
				foreach($hdr_months as $rx){
					$cnts++;
		?>
			<th style="text-align:center;  padding-right: 20px; padding-left: 20px" width="150px">Debit</th>
			<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
			<th style="text-align:center" width="150px">Balance</th>
		<?php
			}
		?>

		<th style="text-align:center;  padding-right: 20px" width="150px">Debit</th>
		<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
		<th style="text-align:center;  padding-right: 20px" width="150px">Balance</th>
  </tr>
 
 <?php

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$GtotDrBeg = 0;
	$GtotCrBeg = 0;

	$rowdebit = 0;
	$rowcredit = 0;
	$nrowttal = 0;

	$GRowDr = 0;
	$GRowCr = 0;
	$GRowCrTot = 0;
	
	foreach($hdr_accts as $rx)
	{

?>
   <tr>
    <td>&nbsp;</td>
    <td onclick="funcset('<?=$rx?>', '<?= $dteyr ?>')" style="cursor: pointer; padding-right: 20px" nowrap><?php echo $rx;?></td>
    <td onclick="funcset('<?=$rx?>', '<?= $dteyr ?>')" style="cursor: pointer; padding-right: 20px" nowrap><?php echo $qry_acctsnames[$rx];?></td>

	<!-- Beg Bal -->
	<?php
		if(isset($begtotdebit[$rx])){
			$ndramt = $begtotdebit[$rx];
		}else{
			$ndramt = 0;
		}

		if(isset($begtotcredit[$rx])){
			$ncramt = $begtotcredit[$rx];
		}else{
			$ncramt = 0;
		}

		$ntotbal = floatval($ndramt) - floatval($ncramt);

		$GtotDrBeg = $GtotDrBeg + floatval($ndramt);
		$GtotCrBeg = $GtotCrBeg + floatval($ncramt);

		$rowdebit = $rowdebit + $ndramt;
		$rowcredit = $rowcredit + $ncramt;
	?>
	<td style="text-align:right; padding-right: 20px"><?=(floatval($ndramt)!=0) ? number_format($ndramt,2) : ""?></td>
    <td style="text-align:right; padding-right: 20px"><?=(floatval($ncramt)!=0) ? number_format($ncramt,2) : ""?></td>
	<td style="text-align:right; padding-right: 20px; border-right: 1px solid #000">
		<?php
		
			if($ntotbal < 0) {
				echo "(".number_format(abs($ntotbal),2).")";
			}elseif($ntotbal > 0) {					
				echo number_format($ntotbal,2);
			}else{
				echo "";
			}
		?>
	</td>
	<!-- End Beg Bal -->
		<?php
				$cnts = 0;
				$ndramt = 0;
				$ncramt = 0;
				foreach($hdr_months as $rz){
					$cnts++;

					if(isset($qrytotdebit[$rz][$rx])){
						$ndramt = $qrytotdebit[$rz][$rx];
					}else{
						$ndramt = 0;
					}

					if(isset($qrytotcredit[$rz][$rx])){
						$ncramt = $qrytotcredit[$rz][$rx];
					}else{
						$ncramt = 0;
					}

					$ntotbal = floatval($ndramt) - floatval($ncramt);

					$GtotDr[$rz] = $GtotDr[$rz] + floatval($ndramt);
					$GtotCr[$rz] = $GtotCr[$rz] + floatval($ncramt);

					$rowdebit = $rowdebit + $ndramt;
					$rowcredit = $rowcredit + $ncramt;
			
		?>

  	<td style="text-align:right; padding-right: 20px; padding-left: 20px"><?=(floatval($ndramt)!=0) ? number_format($ndramt,2) : ""?></td>
    <td style="text-align:right; padding-right: 20px"><?=(floatval($ncramt)!=0) ? number_format($ncramt,2) : ""?></td>
	<td style="text-align:right; padding-right: 20px; border-right: 1px solid #000">
		<?php
			if($ntotbal < 0) {
				echo "(".number_format(abs($ntotbal),2).")";
			}elseif($ntotbal > 0) {					
				echo number_format($ntotbal,2);
			}else{
				echo "";
			}
		?>
	</td>

		<?php
				}

		?>

	<td style="text-align:right; padding-right: 20px; padding-left: 20px"><?=(floatval($rowdebit)!=0) ? number_format($rowdebit,2) : ""?></td>
    <td style="text-align:right; padding-right: 20px"><?=(floatval($rowcredit)!=0) ? number_format($rowcredit,2) : ""?></td>
	<td style="text-align:right; padding-right: 20px; border-right: 1px solid #000">
		<?php

			$GRowDr =  $GRowDr + floatval($rowdebit);
			$GRowCr = $GRowCr + floatval($rowcredit);

		    $nrowttal = floatval($rowdebit) - floatval($rowcredit);
			if($nrowttal < 0) {
				echo "(".number_format(abs($nrowttal),2).")";
			}elseif($nrowttal > 0) {					
				echo number_format($nrowttal,2);
			}else{
				echo "";
			}


			$rowdebit = 0;
			$rowcredit = 0;
			$nrowttal = 0;
		?>
	</td>

  </tr>
<?php
	}
?>
 
    <tr>
    	<td>&nbsp;</th>
      	<td colspan="2"><b>TOTALS: </b></th> 

			<?php
				$ntotGBalBeg = $GtotDrBeg - $GtotCrBeg;
			?>
		  	<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotDrBeg), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotCrBeg), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?php echo number_format(floatval($ntotGBalBeg), 2);?></b></th>

			<?php
				foreach($hdr_months as $rz){
					$ntotGBal = 0;

					$GtotDr[$rz] = $GtotDr[$rz] + floatval($ndramt);
					$GtotCr[$rz] = $GtotCr[$rz] + floatval($ncramt);

					$ntotGBal = $GtotDr[$rz] - $GtotCr[$rz];
			?>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotDr[$rz]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotCr[$rz]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?php echo number_format(floatval($ntotGBal), 2);?></b></th>
			<?php
				}

				$GRowCrTot = $GRowDr - $GRowCr;
			?> 
			
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GRowDr), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GRowCr), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?php echo number_format(floatval($GRowCrTot), 2);?></b></th>

		</tr>
 
</table>



<?php

		$sql = "Select A.cmodule, A.ctranno, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
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
<br><br><br><br>
<form action="TBal_Det.php" name="frmdet" id="frmdet" target="_blank" method="POST">
	<input type="hidden" name="ccode" id="ccode" value="">
	<input type="hidden" name="date1" id="date1" value="">
</form>

</body>
</html>

<script>
	function funcset(xcode, xdte1){
		document.getElementById("ccode").value = xcode;
		document.getElementById("date1").value = xdte1;
	
		document.getElementById("frmdet").submit(); 
	}
</script>