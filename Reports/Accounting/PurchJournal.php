<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "PurchReg.php";

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
		$compadd = $row['compadd'];
		$comptin = $row['comptin'];
	}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Purchase Register</title>
</head>

<body style="padding:10px">
<h3><b>Company: <?=strtoupper($compname);  ?></b></h3>
<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
<h3><b>Kind of Book: Purchase Journal</b></h3>
<h3><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h3>


<br><br>
<table width="100%" border="0" align="center">
  <tr>
    <th>Date</th>
    <th>Suppplier TIN.</th>
    <th colspan="2">Supplier's Name</th>
    <th>Address</th>
    <th>Description</th>
    <th>Reference</th>
    <th style="text-align: right">Amount</th>
	<th style="text-align: right">Discount</th>
	<th style="text-align: right">VAT Amount</th>
	<th style="text-align: right">Net Purchases</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$sql = "select a.dreceived as dcutdate, a.ctranno as csalesno, a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.lcancelled, a.lapproved, a.lvoid
From suppinv a
left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join suppliers c on a.ccode=c.ccode and a.compcode=c.compcode
where a.compcode='$company' and a.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
order by a.dreceived, a.ctranno, b.ndebit desc";
//echo $sql;

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$salesno = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totDebit=0;	
	$totCredit=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		//if($salesno==""){
			//$salesno = $row['csalesno'];
		//}
		
		if($salesno!=$row['csalesno']){
			$code = $row['ccode'];
			$name= $row['cname'];
			$invval = $row['csalesno'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}
		
		if($row['lcancelled']==1){
			?>
            <tr <?php echo $classcode;?>>
                <td><?php echo $dateval;?></td>
                <td><?php echo $invval;?></td>
                <td><?php echo $code;?></td>
                <td><?php echo $name;?></td>
                <td colspan="4" align="center">- C A N C E L L E D -</td>
            </tr>
            <?php
		}elseif($row['lvoid']==1){
			?>
            <tr <?php echo $classcode;?>>
                <td><?php echo $dateval;?></td>
                <td><?php echo $invval;?></td>
                <td><?php echo $code;?></td>
                <td><?php echo $name;?></td>
                <td colspan="4" align="center">- V O I D E D -</td>
            </tr>
            <?php
		}
		elseif($row['lapproved']==0){
			?>
            <tr <?php echo $classcode;?>>
                <td><?php echo $dateval;?></td>
                <td><?php echo $invval;?></td>
                <td><?php echo $code;?></td>
                <td><?php echo $name;?></td>
                <td colspan="4" align="center">- NOT YET POSTED -</td>
            </tr>
            <?php
		}
		else{
?>  
  <tr <?php echo $classcode;?>>
    <td><?php echo $dateval;?></td>
    <td><?php echo $invval;?></td>
    <td><?php echo $code;?></td>
    <td><?php echo $name;?></td>
    <td><?php echo $row['acctno'];?></td>
    <td><?php echo $row['ctitle'];?></td>
    <td align="right"><?php echo (($row['ndebit'] > 0) ? $row['ndebit'] : '');?></td>
    <td align="right"><?php echo (($row['ncredit'] > 0) ? $row['ncredit'] : '');?></td>
  </tr>
<?php 
		$code = "";
		$name= "";
		$invval = "";
		$dateval="";		
		$classcode="";		
		$salesno=$row['csalesno'];
		$totDebit=$row['ndebit']+$totDebit;	
		$totCredit=$row['ncredit']+$totCredit;
		}
	}
?>

    <tr class='rptGrand'>
    	<td colspan="7" align="right"><b>G R A N D&nbsp;&nbsp;T O T A L:</b></td>
        <td align="right"><b><?php echo $totDebit;?></b></td>
        <td align="right"><b><?php echo $totCredit;?></b></td>
		<td align="right"><b><?php echo $totCredit;?></b></td>
		<td align="right"><b><?php echo $totCredit;?></b></td>
    </tr>
</table>

</body>
</html>