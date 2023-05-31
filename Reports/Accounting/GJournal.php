<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "GJournal.php";

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
<title>General Journal</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>General Journal</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding = "3">
  <tr>
    <th rowspan="2" width="50px">Module</th>
		<th rowspan="2" width="50px">Transaction No.</th>
    <th rowspan="2" style="text-align:center" width="100px">Account No. </th>
    <th rowspan="2" style="text-align:center">Account Name</th>
    <th colspan="2" style="text-align:center">Amount</th>
  </tr>
  <tr>
  	<th style="text-align:center"  width="150px">Debit</th>
    <th style="text-align:center"  width="150px">Credit</th>
  </tr>
 
 <?php

	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			Where A.compcode='$company' and A.ddate between STR_TO_DATE('".$_REQUEST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_REQUEST['date2']."', '%m/%d/%Y')
			Order By A.dpostdate, A.ctranno, A.ndebit desc, A.ncredit desc";

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$tranno = "";
	$ecode = "";
	$cmod = "";

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cntr++;

			if($tranno!==$row['ctranno']){
				$cmod = $row['cmodule'];
				$ecode = $row['ctranno'];

				if($cntr>1){
					echo "<tr><td colspan ='4' align='right'>&nbsp;</td><td style='text-align:right; border-top: 2px solid !important'><b>".number_format($ntotdebit,2)."</b></td><td style='text-align:right; border-top: 2px solid !important'><b>".number_format($ntotcredit,2)."</b></td><tr>";

					$ntotdebit = 0;
					$ntotcredit = 0;
				}
			}

?>
   <tr>
		<td <?=($cntr>1 && $ecode !== "") ? "style='border-top: 2px solid !important'" : ""?>><?=$cmod;?></td>
		<td <?=($cntr>1 && $ecode !== "") ? "style='border-top: 2px solid !important'" : ""?>><?=$ecode;?></td>
    <td <?=($cntr>1 && $ecode !== "") ? "style='border-top: 2px solid !important'" : ""?>><?php echo $row['acctno'];?></td>
    <td <?=($cntr>1 && $ecode !== "") ? "style='border-top: 2px solid !important'" : ""?>><?php echo $row['cacctdesc'];?></td>
  	<td style="text-align:right <?=($cntr>1 && $ecode !== "") ? "; border-top: 2px solid !important" : ""?>"><?=(floatval($row['ndebit'])<>0) ? number_format(floatval($row['ndebit']), 2) : ""?></td>
    <td style="text-align:right <?=($cntr>1 && $ecode !== "") ? "; border-top: 2px solid !important" : ""?>"><?=(floatval($row['ncredit'])<>0) ? number_format(floatval($row['ncredit']), 2) : ""?></td>
  </tr>
<?php
		$cmod = "";
		$ecode = "";
		$tranno = $row['ctranno'];

		$ntotdebit = $ntotdebit + floatval($row['ndebit']);
		$ntotcredit = $ntotcredit + floatval($row['ncredit']);


	}


	echo "<tr><td colspan ='4' align='right'>&nbsp;</td><td style='text-align:right; border-top: 2px solid !important'><b>".number_format($ntotdebit,2)."</b></td><td style='text-align:right; border-top: 2px solid !important'><b>".number_format($ntotcredit,2)."</b></td><tr>";
?>

 
</table>

</body>
</html>