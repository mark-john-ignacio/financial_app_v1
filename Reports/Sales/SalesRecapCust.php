<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "SalesReg.php";

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
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sales Register</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Recapitulation Per Customer: Sales Register</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="80%" border="0" align="center">
  <tr>
    <th colspan="2">Customer</th>
    <th>Account No.</th>
    <th>Account Title</th>
    <th>Debit</th>
    <th>Credit</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$sql = "select  A.ccode, A.cname, A.acctno, A.ctitle, Sum(A.ncredit) as ncredit, Sum(A.ndebit) as ndebit
FROM
(
select  a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit
From sales a
left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
) A
group by A.ccode, A.cname, A.acctno, A.ctitle
order by A.ccode, sum(A.ndebit) desc";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$codeval = "";
	$code = "";
	$name= "";
	$classcode="";
	$totDebit=0;	
	$totCredit=0;
	$totDebitGRAND=0;	
	$totCreditGRAND=0;
	$ctr = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{

		
		if($code!=$row['ccode']){
			$ctr = $ctr + 1;
			$codeval = $row['ccode'];
			$name= $row['cname'];
			$classcode="class='rpthead'";
			
		}
		
		if($code!=$row['ccode'] and $ctr>1){
			
			if(abs(floatval($totDebit)-floatval($totCredit)) >= 1){
				$classcode="class='rpterror'";
			}
			else{
				$classcode="";
			}
		
?>  
          <tr <?php echo $classcode;?>>
            <td colspan="4">&nbsp;</td>
            <td align="right" class="rpttot"><?php echo (($totDebit > 0) ? number_format($totDebit,4) : '');?></td>
            <td align="right" class="rpttot"><?php echo (($totCredit > 0) ? number_format($totCredit,4) : '');?></td>
          </tr>
  
  <?php 
			$totDebit=0;	
			$totCredit=0;
		
		  
		  }

			$totDebit = $totDebit + $row['ndebit'];
			$totCredit = $totCredit + $row['ncredit'];
			$totDebitGRAND=$totDebitGRAND + $row['ndebit'];	
			$totCreditGRAND=$totCreditGRAND + $row['ncredit'];
  
   ?>
  <tr <?php //echo $classcode;?>>
    <td><?php echo $codeval;?></td>
    <td><?php echo $name;?></td>
    <td><?php echo $row['acctno'];?></td>
    <td><?php echo $row['ctitle'];?></td>
    <td align="right"><?php echo (($row['ndebit'] > 0) ? number_format($row['ndebit'],4) : '');?></td>
    <td align="right"><?php echo (($row['ncredit'] > 0) ? number_format($row['ncredit'],4) : '');?></td>
  </tr>
<?php 
		$codeval = "";
		$name= "";
		$classcode="";	
		$code=$row['ccode']	;
		//$totDebit=$row['ndebit']+$totDebit;	
		//$totCredit=$row['ncredit']+$totCredit;

	}
?>

          <tr<?php echo $classcode;?>> 
            <td colspan="4">&nbsp;</td>
            <td align="right" class="rpttot"><?php echo (($totDebit > 0) ? number_format($totDebit,4) : '');?></td>
            <td align="right" class="rpttot"><?php echo (($totCredit > 0) ? number_format($totCredit,4) : '');?></td>
          </tr>


          <tr class="rptGrand">
            <td colspan="4" align="right"><b>GRAND TOTAL:</b></td>
            <td align="right"><b><?php echo (($totDebitGRAND > 0) ? number_format($totDebitGRAND,4) : '');?></b></td>
            <td align="right"><b><?php echo (($totCreditGRAND > 0) ? number_format($totCreditGRAND,4) : '');?></b></td>
          </tr>

</table>

</body>
</html>