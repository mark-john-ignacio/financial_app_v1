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

	if($trantype=="Trade"){
		$tblhdr = "so";
		$tbldtl = "so_t";
	}elseif($trantype=="Non-Trade"){
		$tblhdr = "ntso";
		$tbldtl = "ntso_t";
	}

	if($trantype!==""){
		$xsql = "select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
		From ".$tbldtl." a	
		left join ".$tblhdr." b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
		".$qryposted.$qrycust."
		order by a.ctranno, a.nident";


	}else{
		$xsql = "Select A.nident, A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.namount
		From (
			select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
			From so_t a	
			left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
			".$qryposted.$qrycust."

			UNION ALL

			select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
			From ntso_t a	
			left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
			".$qryposted.$qrycust."
		) A 
		order by A.ctranno, A.nident";
		
	}
	
	$finarray =  array();
	$itmslist = array();
	$custslist = array();
	$result=mysqli_query($con,$xsql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;

		if (!in_array($row['citemno'], array_column($itmslist, 'citemno'))) {
			$itmslist[] = array('citemno' => $row['citemno'], 'citemdesc' => $row['citemdesc'], 'cunit' => $row['cunit']);
		}

		if (!in_array($row['ccode'], array_column($custslist, 'ccode'))) {
			$custslist[] = array('ccode' => $row['ccode'], 'cname' => $row['cname']);
		}
	}


	$resDR=mysqli_query($con,"Select A.ctranno, A.nident, B.ccode, A.creference, A.crefident, A.citemno, A.nqty from dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') UNION ALL Select A.ctranno, A.nident, B.ccode, A.creference, A.crefident, A.citemno, A.nqty from ntdr_t A left join ntdr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')");
	$findr = array();
	while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
		$findr[] = $row;
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Discrepancy Report - SO vs DR</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Discrepancy Report - SO vs DR</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="1" align="center" cellpadding="5px">
	<thead>
		<tr>
			<th nowrap rowspan="2">Item Code</th>
			<th nowrap rowspan="2">Item Desc</th>
			<th nowrap rowspan="2">UOM</th>
			<?php
				foreach($custslist as $rocut){
			?>
				<td nowrap align="center" colspan="3"><b><?=$rocut['cname']?></b></td>
			<?php
				}
			?>

			<td nowrap align="center" rowspan="2"><b>Total Order</b></td>
			<td nowrap align="center" rowspan="2"><b>Total Dispatch</b></td>
			<td nowrap align="center" rowspan="2"><b>Total Discrepancy</b></td>
		</tr>

		<tr>
			<?php
				foreach($custslist as $rocut){
			?>
				<td nowrap align="center"><b>SO</b></td>
				<td nowrap align="center"><b>DR</b></td>
				<td nowrap align="center"><b>VARIANCE</b></td>
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