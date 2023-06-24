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
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Trial Balance</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Trial Balance</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th rowspan="2" width="50px">&nbsp;</th>
    <th rowspan="2" style="text-align:center" width="100px">Account No. </th>
    <th rowspan="2" style="text-align:center">Account Name</th>
    <th colspan="2" style="text-align:center">Amount</th>
  </tr>
  <tr>
  	<th style="text-align:center"  width="150px">Debit</th>
    <th style="text-align:center"  width="150px">Credit</th>
  </tr>
 
 <?php

	$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.acctno=B.cacctid
			Group By A.acctno, B.cacctdesc
			Order By A.acctno";

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
			$ntotdebit = $ntotdebit + floatval($row['ndebit']);
			$ntotcredit = $ntotcredit + floatval($row['ncredit']);
	
?>
   <tr>
    <td>&nbsp;</td>
    <td><?php echo $row['acctno'];?></td>
    <td><?php echo $row['cacctdesc'];?></td>
  	<td style="text-align:right"><?php if (floatval($row['ndebit'])<>0) { echo number_format(floatval($row['ndebit']), 2); }?></td>
    <td style="text-align:right"><?php if (floatval($row['ncredit'])<>0) { echo number_format(floatval($row['ncredit']), 2); }?></td>
  </tr>
<?php
	}
?>
 
     <tr>
    	<td style="padding-top:10px">&nbsp;</td>
     <tr>
    	<td>&nbsp;</th>
        <td colspan="2"><b>TOTALS: </b></th>
        <td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotdebit), 2);?></b></th>
        <td  style="text-align:right; border-top:1px solid; border-bottom:5px double"><b><?php echo number_format(floatval($ntotcredit), 2);?></b></th>
	</td>
 
</table>

</body>
</html>