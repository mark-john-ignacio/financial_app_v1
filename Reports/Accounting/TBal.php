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
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Trial Balance</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="70%" border="0" align="center" class="my-table">
  <tr>
    <th rowspan="2" width="50px">&nbsp;</th>
    <th rowspan="2" style="text-align:center" width="100px">Account No. </th>
    <th rowspan="2" style="text-align:center">Account Name</th>
    <th colspan="2" style="text-align:center">Amount</th>
		<th rowspan="2" style="text-align:center" width="150px">Balance</th>
  </tr>
  <tr>
  	<th style="text-align:center"  width="150px">Debit</th>
    <th style="text-align:center"  width="150px">Credit</th>
  </tr>
 
 <?php

	$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode='$company' and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
			Group By A.acctno, B.cacctdesc
			Order By A.acctno";

		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
			$ntotdebit = $ntotdebit + floatval($row['ndebit']);
			$ntotcredit = $ntotcredit + floatval($row['ncredit']);

			$ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

			$ntotGBal = $ntotGBal + $ntotbal;
	
?>
   <tr>
    <td>&nbsp;</td>
    <td onclick="funcset('<?=$row['acctno']?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer "><?php echo $row['acctno'];?></td>
    <td onclick="funcset('<?=$row['acctno']?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer "><?php echo $row['cacctdesc'];?></td>
  	<td style="text-align:right"><?php if (floatval($row['ndebit'])<>0) { echo number_format(floatval($row['ndebit']), 2); }?></td>
    <td style="text-align:right"><?php if (floatval($row['ncredit'])<>0) { echo number_format(floatval($row['ncredit']), 2); }?></td>
		<td style="text-align:right"><?=$ntotbal < 0 ? "(".number_format(abs($ntotbal),2).")" : number_format($ntotbal,2) ?></td>
  </tr>
<?php
	}
?>
 
    <tr>
    	<td>&nbsp;</th>
      <td colspan="2"><b>TOTALS: </b></th>
      <td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotdebit), 2);?></b></th>
      <td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotcredit), 2);?></b></th>
			<td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotGBal), 2);?></b></th>
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