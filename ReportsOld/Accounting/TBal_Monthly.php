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
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

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
  </tr>
  <tr>
		<?php
				$cnts = 0;
				foreach($hdr_months as $rx){
					$cnts++;
		?>
			<th style="text-align:center;  padding-right: 20px <?=($cnts > 1) ? "; padding-left: 20px" : ""?>" width="150px">Debit</th>
			<th style="text-align:center;  padding-right: 20px" width="150px">Credit</th>
			<th style="text-align:center" width="150px">Balance</th>
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
	foreach($hdr_accts as $rx)
	{

?>
   <tr>
    <td>&nbsp;</td>
    <td onclick="funcset('<?=$rx?>')" style="cursor: pointer; padding-right: 20px" nowrap><?php echo $rx;?></td>
    <td onclick="funcset('<?=$rx?>')" style="cursor: pointer; padding-right: 20px" nowrap><?php echo $qry_acctsnames[$rx];?></td>

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
			
		?>

  	<td style="text-align:right; padding-right: 20px <?=($cnts > 1) ? "; padding-left: 20px" : ""?>"><?=(floatval($ndramt)!=0) ? number_format($ndramt,2) : ""?></td>
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
  </tr>
<?php
	}
?>
 
    <tr>
    	<td>&nbsp;</th>
      <td colspan="2"><b>TOTALS: </b></th>
			<?php
				foreach($hdr_months as $rz){
					$ntotGBal = 0;

					$GtotDr[$rz] = $GtotDr[$rz] + floatval($ndramt);
					$GtotCr[$rz] = $GtotCr[$rz] + floatval($ncramt);

					$ntotGBal = $GtotDr[$rz] - $GtotCr[$rz];
			?>
      	<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?=number_format(floatval($GtotDr[$rz]), 2);?></b></th>
      	<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?=number_format(floatval($GtotCr[$rz]), 2);?></b></th>
				<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotGBal), 2);?></b></th>
			<?php
				}
			?>
		</tr>
 
</table>

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