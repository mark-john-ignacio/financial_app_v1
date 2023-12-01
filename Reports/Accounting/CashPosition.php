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

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];


	$resDR=mysqli_query($con,"Select A.ctranno, A.nident, B.ccode, A.creference, A.crefident, A.citemno, A.nqty from dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') UNION ALL Select A.ctranno, A.nident, B.ccode, A.creference, A.crefident, A.citemno, A.nqty from ntdr_t A left join ntdr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')");
	$findr = array();
	while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
		$findr[] = $row;
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cash Position</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2><?=($_POST["date1"]==$_POST["date2"]) ? "Daily " : ""?>Cash Position</h2>
<?php
	if($_POST["date1"]==$_POST["date2"]){
?>
	<h3>For <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?></h3>	
<?php
	}else{
?>
	<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
<?php
	}
?>
<br>
</center>

<br><br>
<table width="100%" border="1" align="center" cellpadding="5px">
	<thead>
		<tr>
			<th nowrap rowspan="2">Date</th>
			<th nowrap rowspan="2">Reference</th>
			<th nowrap rowspan="2">Particulars</th>
			<?php
				foreach($custslist as $rocut){
			?>
				<td nowrap align="center" colspan="3"><b><?=$rocut['cname']?></b></td>
			<?php
				}
			?>

		</tr>

		<tr>
			<?php
				foreach($custslist as $rocut){
			?>
				<td nowrap align="center"><b>Debit</b></td>
				<td nowrap align="center"><b>Credit</b></td>
				<td nowrap align="center"><b>Balance</b></td>
			<?php
				}
			?>
		</tr>
	</thead>
  
<?php
	$totGrossSO = 0;
	$totGrossDR = 0;
	foreach($itmslist as $row)
	{

		
?>  
  <tr>
    <td nowrap><?php echo $row['citemno'];?></td>
    <td nowrap><?php echo $row['citemdesc'];?></td>
    <td nowrap><?php echo $row['cunit'];?></td>
    <?php
			foreach($custslist as $rocut){
		?>
				<td nowrap align="center">
					<?php
						$totSO = 0;
						foreach($finarray as $roworder){
							if($roworder['citemno']==$row['citemno'] && $roworder['ccode']==$rocut['ccode']){
								$totSO = $totSO + floatval($roworder['nqty']);
								$totGrossSO = $totGrossSO + floatval($roworder['nqty']);
							}
						}

						echo ($totSO==0) ? "" : number_format($totSO);
					?>
				</td>
				<td nowrap align="center">
					<?php
						$totDR = 0;
						foreach($findr as $rowdrs){
							if($rowdrs['citemno']==$row['citemno'] && $rowdrs['ccode']==$rocut['ccode']){
								$totDR = $totDR + floatval($rowdrs['nqty']);
								$totGrossDR = $totGrossDR + floatval($rowdrs['nqty']);
							}
						}

						echo ($totDR==0) ? "" : number_format($totDR);

					?>
				</td>
				<td nowrap align="center">
					<?php
						$xdvar = floatval($totDR) - floatval($totSO);

						echo ($xdvar==0) ? "" : number_format($xdvar);
					?>
				</td>
		<?php
			}
		?>
    <td nowrap align="center">
			<b>
				<?php
					echo ($totGrossSO==0) ? "" : number_format($totGrossSO);
				?>
			</b>
		</td>
		<td nowrap align="center">
			<b>
				<?php
					echo ($totGrossDR==0) ? "" : number_format($totGrossDR);
				?>
			</b>
		</td>
		<td nowrap align="center">
			<b>
				<?php
					$xdvartot = floatval($totGrossDR) - floatval($totGrossSO);

					echo ($xdvartot==0) ? "" : number_format($xdvartot);
				?>
			</b>
		</td>
  </tr>
<?php 
		$totGrossSO = 0;
		$totGrossDR = 0;
	}
?>


</table>

</body>
</html>