<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "TBal.php";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$arrcomps = array();
	$arrcompsname = array();
	
	$company = $_SESSION['companyid'];
	$sql = "select compcode, compname From company";
	$result=mysqli_query($con,$sql);
  	$companycnt=mysqli_num_rows($result);
	if($companycnt>0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrcomps[] = $row;
			$arrcompsname[] = $row['compname'];
		}
	}

	$totalcolscomp = $companycnt * 3;

	$dteyr = $_POST["selyr"];

	$sql = "Select A.compcode, MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
		Group By A.compcode, MONTH(ddate), A.acctno, B.cacctdesc
		Order By A.compcode, A.acctno, MONTH(ddate)";

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


		$qrytotdebit[$row['compcode']][$row['dmonth']][$row['acctno']] = $row['ndebit'];
		$qrytotcredit[$row['compcode']][$row['dmonth']][$row['acctno']] = $row['ncredit'];

		$qryrows[] = $row;
	}


	//For Beg Bal
	$dteyrminus = $dteyr - 1;
	$begtotdebit = array();
	$begtotcredit = array();

	$sql = "Select A.compcode, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
	From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
	where YEAR(A.ddate) = '$dteyrminus' and IFNULL(B.cacctdesc,'') <> ''
	Group By A.compcode, A.acctno, B.cacctdesc
	Order By A.compcode, A.acctno";

	$result=mysqli_query($con,$sql);
	$qryrows = array();
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if(!in_array($row['acctno'], $qry_accts)){
			$qry_accts[] = $row['acctno'];
			$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];
		}

		$begtotdebit[$row['compcode']][$row['acctno']] = $row['ndebit'];
		$begtotcredit[$row['compcode']][$row['acctno']] = $row['ncredit'];

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
<h2>Company: <?=implode(", ",$arrcompsname);  ?></h2>
<h2>Trial Balance - Monthly (Consolidated)</h2>
<h3>For the Year <?=$dteyr?></h3>
</center>

<br><br>
<table border="0" align="center" class="my-table">
  <tr>
    <th rowspan="3" width="50px">&nbsp;</th>
    <th rowspan="3" style="text-align:center" width="100px">Account No. </th>
    <th rowspan="3" style="text-align:center">Account Name</th>
	<th colspan="<?=$totalcolscomp?>" style="text-align:center">Beginning Balance (<?=$dteyrminus?>)</th>
	<?php
		foreach($hdr_months as $rx){
			$monthNum  = $rx;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('F');
	?>
    	<th colspan="<?=$totalcolscomp?>"  style="text-align:center"><?=$monthName?></th>		
	<?php
		}
	?>
	<th colspan="<?=$totalcolscomp?>" style="text-align:center">Grand Total</th>
  </tr>
  <tr>
	<?php
		$xcnt = 0;
		foreach($arrcomps as $row){
			$xcnt++;
			$style = "";
			if($xcnt==$companycnt){
				$style = "; border-right: 1px solid ";
			}
	?>
		<th colspan="3"  style="text-align:center<?=$style?>"> <?=$row['compname']?></th>
	<?php
		}

		$GtotDr = array();
		$GtotCr = array();
		foreach($hdr_months as $rx){
			$xcnt = 0;
			foreach($arrcomps as $row){
				$xcnt++;
				$style = "";
				if($xcnt==$companycnt){
					$style = "; border-right: 1px solid ";
				}

				$GtotDr[$row['compcode']][$rx] = 0;
				$GtotCr[$row['compcode']][$rx] = 0;
	?>
			<th colspan="3"  style="text-align:center<?=$style?>"> <?=$row['compname']?></th>
	<?php
			}
		}

		foreach($arrcomps as $row){
	?>
		<th colspan="3"  style="text-align:center"> <?=$row['compname']?></th>
	<?php
		}
	?>
  </tr>
  <tr>
	
	<?php
		$xcnt = 0;
		foreach($arrcomps as $row){
			$xcnt++;
			$style = "";
			if($xcnt==$companycnt){
				$style = "; border-right: 1px solid ";
			}
	?>
	<th style="text-align:center;  padding-right: 20px" width="150px">Debit</th>
	<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
	<th style="text-align:center;  padding-right: 20px<?=$style?>" width="150px">Balance</th>

	<?php
		}


			$cnts = 0;
			foreach($hdr_months as $rx){
				$cnts++;

				$xcnt = 0;
				foreach($arrcomps as $row){
					$xcnt++;
					$style = "";
					if($xcnt==$companycnt){
						$style = "; border-right: 1px solid ";
					}
	?>
		<th style="text-align:center;  padding-right: 20px; padding-left: 20px" width="150px">Debit</th>
		<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
		<th style="text-align:center<?=$style?>" width="150px">Balance</th>

	<?php
				}
		}

		foreach($arrcomps as $row){
	?>

		<th style="text-align:center;  padding-right: 20px; padding-left: 20px" width="150px">Debit</th>
		<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
		<th style="text-align:center<?=$style?>" width="150px">Balance</th>
	<?php
		}
	?>

  </tr>
 
 <?php

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$GtotDrBeg = array();
	$GtotCrBeg = array();

	$rowdebit = array();
	$rowcredit = array();
	$nrowttal = 0;

	$GRowDr = array();
	$GRowCr = array();
	$GRowCrTot = 0;

	foreach($arrcomps as $row){
		$GtotDrBeg[$row['compcode']] = 0;
		$GtotCrBeg[$row['compcode']] = 0;

		$rowdebit[$row['compcode']] = 0;
		$rowcredit[$row['compcode']] = 0;

		$GRowDr[$row['compcode']] = 0;
		$GRowCr[$row['compcode']] = 0;
	}

	foreach($hdr_accts as $rx)
	{

?>
   <tr>
    <td>&nbsp;</td>
    <td onclick="funcset('<?=$rx?>', '<?= $dteyr ?>')" style="cursor: pointer; padding-right: 20px" nowrap><?php echo $rx;?></td>
    <td onclick="funcset('<?=$rx?>', '<?= $dteyr ?>')" style="cursor: pointer; padding-right: 20px" nowrap><?php echo $qry_acctsnames[$rx];?></td>

	<!-- Beg Bal -->
	<?php
		$xcnt = 0;
		foreach($arrcomps as $row){
			$xcnt++;
			$style = "";
			if($xcnt==$companycnt){
				$style = "; border-right: 1px solid ";
			}

			
			if(isset($begtotdebit[$row['compcode']][$rx])){
				$ndramt = $begtotdebit[$row['compcode']][$rx];
			}else{
				$ndramt = 0;
			}

			if(isset($begtotcredit[$row['compcode']][$rx])){
				$ncramt = $begtotcredit[$row['compcode']][$rx];
			}else{
				$ncramt = 0;
			}

			$ntotbal = floatval($ndramt) - floatval($ncramt);

			$GtotDrBeg[$row['compcode']] = $GtotDrBeg[$row['compcode']] + floatval($ndramt);
			$GtotCrBeg[$row['compcode']] = $GtotCrBeg[$row['compcode']] + floatval($ndramt);

			$rowdebit[$row['compcode']] = $rowdebit[$row['compcode']] + floatval($ndramt);
			$rowcredit[$row['compcode']] = $rowcredit[$row['compcode']] + floatval($ncramt);
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
	<!-- End Beg Bal -->
		<?php
				$cnts = 0;
				$ndramt = 0;
				$ncramt = 0;
				foreach($hdr_months as $rz){
					$cnts++;

					foreach($arrcomps as $row){

						if(isset($qrytotdebit[$row['compcode']][$rz][$rx])){
							$ndramt = $qrytotdebit[$row['compcode']][$rz][$rx];
						}else{
							$ndramt = 0;
						}

						if(isset($qrytotcredit[$row['compcode']][$rz][$rx])){
							$ncramt = $qrytotcredit[$row['compcode']][$rz][$rx];
						}else{
							$ncramt = 0;
						}

						$ntotbal = floatval($ndramt) - floatval($ncramt);

						$GtotDr[$row['compcode']][$rz] = $GtotDr[$row['compcode']][$rz] + floatval($ndramt);
						$GtotCr[$row['compcode']][$rz] = $GtotCr[$row['compcode']][$rz] + floatval($ncramt);

						$rowdebit[$row['compcode']] = $rowdebit[$row['compcode']] + floatval($ndramt);
						$rowcredit[$row['compcode']] = $rowcredit[$row['compcode']] + floatval($ncramt);
			
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
				}
		?>

	<?php
		foreach($arrcomps as $row){
	?>
		<td style="text-align:right; padding-right: 20px; padding-left: 20px"><?=(floatval($rowdebit[$row['compcode']])!=0) ? number_format($rowdebit[$row['compcode']],2) : ""?></td>
		<td style="text-align:right; padding-right: 20px"><?=(floatval($rowcredit[$row['compcode']])!=0) ? number_format($rowcredit[$row['compcode']],2) : ""?></td>
		<td style="text-align:right; padding-right: 20px; border-right: 1px solid #000">
			<?php
				$GRowDr[$row['compcode']] =  $GRowDr[$row['compcode']] + floatval($rowdebit[$row['compcode']]);
				$GRowCr[$row['compcode']] = $GRowCr[$row['compcode']] + floatval($rowcredit[$row['compcode']]);

				$nrowttal = floatval($rowdebit[$row['compcode']]) - floatval($rowcredit[$row['compcode']]);

				if($nrowttal < 0) {
					echo "(".number_format(abs($nrowttal),2).")";
				}elseif($nrowttal > 0) {					
					echo number_format($nrowttal,2);
				}else{
					echo "";
				}

				$rowdebit[$row['compcode']] = 0;
				$rowcredit[$row['compcode']] = 0;
				$nrowttal = 0;
			?>
		</td>
	<?php
		}
	?>
  </tr>
<?php
	}
?>
 
    <tr>
    	<td>&nbsp;</th>
      	<td colspan="2"><b>TOTALS: </b></th> 

			<?php
			$xcnt = 0;
			foreach($arrcomps as $row){
				$xcnt++;
				$style = "";
				if($xcnt==$companycnt){
					$style = "; border-right: 1px solid ";
				}

				$ntotGBalBeg = $GtotDrBeg[$row['compcode']] - $GtotCrBeg[$row['compcode']];
			?>
		  	<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotDrBeg[$row['compcode']]), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotCrBeg[$row['compcode']]), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?php echo number_format(floatval($ntotGBalBeg), 2);?></b></th>

			<?php
			}

				foreach($hdr_months as $rz){
					foreach($arrcomps as $row){
						$ntotGBal = 0;

						$GtotDr[$row['compcode']][$rz] = $GtotDr[$row['compcode']][$rz] + floatval($ndramt);
						$GtotCr[$row['compcode']][$rz] = $GtotCr[$row['compcode']][$rz] + floatval($ncramt);

						$ntotGBal = $GtotDr[$row['compcode']][$rz] - $GtotCr[$row['compcode']][$rz];
			?>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotDr[$row['compcode']][$rz]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GtotCr[$row['compcode']][$rz]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?php echo number_format(floatval($ntotGBal), 2);?></b></th>
			<?php
					}
				}

				foreach($arrcomps as $row){
					
					$GRowCrTot = $GRowDr[$row['compcode']] - $GRowCr[$row['compcode']];
			?>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GRowDr[$row['compcode']]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?=number_format(floatval($GRowCr[$row['compcode']]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double;  padding-right: 20px"><b><?php echo number_format(floatval($GRowCrTot), 2);?></b></th>
			<?php
				}
			?>
			
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