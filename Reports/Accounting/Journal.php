<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "Journal.php";

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
	$selrp = $_POST["selrpt"];
	$qry = "";
	$varmsg = "";

	if ($selrp=="1"){
		$varmsg = "POSTED TRANSACTIONS";
		$qry = " and b.lapproved=1 ";
	}
	elseif ($selrp=="0"){
		$varmsg = "UNPOSTED TRANSACTIONS";
		$qry = " and b.lapproved=0 ";
	}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Journal Entry</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Journal Entry</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
<h3><?php echo $varmsg;?></h3>
</center>

<br><br>
<table width="100%" border="0" align="center">
  <tr>
  	<th>JE No.</th>
    <th>JE Date</th>
    <th>Date Posted</th>
    <th>Description</th>
    <th>Account Code</th>
    <th>Account Title</th>
    <th>Debit</th>
    <th>Credit</th>
  </tr>
  
<?php

$sql = "Select a.ctranno, a.cacctno, a.ctitle, a.ndebit, a.ncredit, b.djdate, b.ddateposted, b.cmemo
From journal_t a left join journal b on a.compcode = b.compcode and a.ctranno=b.ctranno
Where a.compcode = '$company' and b.djdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
and b.lcancelled=0 ". $qry . " Order By a.ctranno, a.nidentity";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$ctranno = "";
	$ctran = "";
	$ddate = "";
	$ddateposted = "";
	$classcode="";
	$cmemo = "";
	
	$ntotdebit = 0;
	$ntotcredit = 0;
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		if($ctranno!=$row['ctranno']){
			$ctran = $row['ctranno'];
			$ddate = $row['djdate'];
			$cmemo = $row['cmemo'];
			$ddateposted = $row['ddateposted'];
			$classcode="class='rpthead'";
		}
		
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $ctran;?></td>
    <td><?php echo $ddate;?></td>
    <td><?php echo $ddateposted;?></td>
    <td width="300px"><?php echo $cmemo;?></td>
    <td><?php echo $row['cacctno'];?></td>
    <td><?php echo $row['ctitle'];?></td>
    <td align="right"><?php if( $row['ndebit'] != 0) { echo number_format($row['ndebit'],4); } ?></td>
    <td align="right"><?php if( $row['ncredit'] != 0) { echo number_format($row['ncredit'],4); } ?></td>
  </tr>
<?php 

	$ntotdebit = $ntotdebit + $row['ndebit'];
	$ntotcredit = $ntotcredit + $row['ncredit'];

$ctranno=$row['ctranno'];
$ctran="";
$classcode="";
$ddate = "";
$cmemo = "";
$ddateposted = "";
}
?>

    <tr class='rptGrand'>
    	<td colspan="6" align="right"><b>TOTAL</b></td>
        <td style='text-align:right; border-top: 2px solid !important'><b><?php echo number_format($ntotdebit,4);?></b></td>
        <td style='text-align:right; border-top: 2px solid !important'><b><?php echo number_format($ntotcredit,4);?></b></td>
    </tr>
</table>

</body>
</html>