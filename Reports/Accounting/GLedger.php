<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "GLedger.php";

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


$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

function getbalance($cnt, $bal, $ndebit, $ncredit){

}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css?x=<?=time()?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>General Ledger</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>General Ledger</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>

<?php
	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit
	From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
	Where A.compcode='$company' and A.ddate between STR_TO_DATE('".$_REQUEST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_REQUEST['date2']."', '%m/%d/%Y')
	Order By A.acctno, A.dpostdate, A.ctranno, A.ndebit desc, A.ncredit desc";

	$arracctnos = array();
	$arrallqry = array();
	$result=mysqli_query($con,$sql);			
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
	$arrallqry[] = $row;
	$arracctnos[] = array('cacctno' => $row['acctno'], 'cacctdesc' => $row['cacctdesc']);
	}

	$arrundrs = array_intersect_key( $arracctnos , array_unique( array_map('serialize' , $arracctnos ) ) );

	$cntr = 0;
	$dcurrentacct = "";
	foreach($arrundrs as $rowxz){
		$cntr++;
		if($cntr>1){
			echo "<br><br>";
		}

		$dcurrentacct = $rowxz['cacctno'];
?>

<table width="55%" border="0" align="center" cellpadding = "3" class="tbl-serate">
	<tr>
		<th colspan="5">
			<table width="100%" border="0" align="center" cellpadding = "3">
				<tr>
					<td width="30%"><b>Acct ID:</b> <?=$rowxz['cacctno']?></td>
					<td><b>Description:</b> <?=$rowxz['cacctdesc']; ?></td>
					<td width="30%" style="text-align:right"><!--<b>Balance:</b> <?//=$rowxz['cacctdesc']; ?>--></td>
				</tr>
			</table>
		</th>
	</tr>
  <tr>
		<th>Reference</th>
		<th width="100px">Date</th>
    <th style="text-align:right" width="150px">Debit</th>
    <th style="text-align:right" width="150px">Credit</th>
  </tr>

 <?php
	$totdebit = 0;
	$totcredit = 0;
	$cntr = 0;
	$xv = 0;
	foreach($arrallqry as $drow)
	{
		if($drow['acctno']==$rowxz['cacctno']){
			$cntr++;

			$totdebit = $totdebit + floatval($drow['ndebit']);
			$totcredit = $totcredit + floatval($drow['ncredit']);
	?>
   <tr>
		<td><?=$drow['ctranno']?></td>
		<td><?=date_format(date_create($drow['ddate']), "d-M-y")?></td>
  	<td style="text-align:right;"><?=(floatval($drow['ndebit'])<>0) ? number_format(floatval($drow['ndebit']), 2) : ""?></td>
    <td style="text-align:right"><?=(floatval($drow['ncredit'])<>0) ? number_format(floatval($drow['ncredit']), 2) : ""?></td>
		<!--<td style="text-align:right">
			<?php
					//$xv = getbalance($cntr, $xv, $drow['ndebit'], $drow['ncredit']);
					//echo number_format(floatval($xv), 2);
			?>
		</td>-->
  </tr>
	<?php
		}
	}
	?>

	<tr>
		<td style="text-align:right;" colspan="2"><b>Total <?=$dcurrentacct?></b></td>
  	<td style="text-align:right; border-bottom-style: double; border-top: 1px solid"><b><?=(floatval($totdebit)<>0) ? number_format(floatval($totdebit), 2) : ""?></b></td>
    <td style="text-align:right; border-bottom-style: double; border-top: 1px solid"><b><?=(floatval($totcredit)<>0) ? number_format(floatval($totcredit), 2) : ""?></b></td>
		<!--<td>
			&nbsp;
		</td>-->
  </tr>

 
</table>

<?php
	}
?>

</body>
</html>