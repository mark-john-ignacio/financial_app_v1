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
	<link rel="stylesheet" type="text/css" href="../../CSS/cssbordered.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sales Register</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Recapitulation: Sales Register</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
</center>

<br><br>
<table width="70%" border="0" align="center">
  <tr>
    <th>Account No.</th>
    <th>Account Title</th>
    <th>Debit</th>
    <th>Credit</th>
  </tr>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$sql = "Select A.* From
(
SELECT 1 as orderd, A.`acctno`, A.`ctitle`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
From `glactivity` A left join `sales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and A.`ndebit` <> 0
group by A.`acctno`, A.`ctitle`
 
UNION ALL
 
SELECT 2 as orderd, B.`ccode`, C.`cname`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
From `glactivity` A left join `sales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
left join `customers` C on B.`ccode`=C.`cempid` and B.`compcode`=C.`compcode`
Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and A.`ncredit` <> 0
 group by  B.`ccode`, C.`cname`
 
 UNION ALL
 
SELECT 3 as orderd, A.`acctno`, A.`ctitle`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
From `glactivity` A left join `sales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and A.`ncredit` <> 0
 group by  A.`acctno`, A.`ctitle`

 UNION ALL

 SELECT 1 as orderd, A.`acctno`, A.`ctitle`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
From `glactivity` A left join `ntsales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and A.`ndebit` <> 0
group by A.`acctno`, A.`ctitle`
 
UNION ALL
 
SELECT 2 as orderd, B.`ccode`, C.`cname`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
From `glactivity` A left join `ntsales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
left join `customers` C on B.`ccode`=C.`cempid` and B.`compcode`=C.`compcode`
Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and A.`ncredit` <> 0
 group by  B.`ccode`, C.`cname`
 
 UNION ALL
 
SELECT 3 as orderd, A.`acctno`, A.`ctitle`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
From `glactivity` A left join `ntsales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and A.`ncredit` <> 0
 group by  A.`acctno`, A.`ctitle`
 ) A
order by A.orderd, A.`acctno`";

//echo $sql;
$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$acctno="";
	$ctitle="";
	$ndebitTot  = 0;
	$ncreditTot  = 0;
	$cntrtotal  = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		if($row["orderd"]==2){
			
			$acctno = "";
			$ctitle = $row["acctno"]." - ".$row["ctitle"];
			
		}
		else{
			$acctno = $row["acctno"];
			$ctitle = $row["ctitle"];
		}
		
					
	if($row["orderd"]==3 and $cntrtotal == 0){
		$cntrtotal = 1;
?>
  <tr>
    <td colspan="2" class="rptGrand">&nbsp;</td>
    <td align="right" class="rptGrand"><?php echo (($ndebitTot > 0) ? number_format($ndebitTot,2) : '');?></td>
    <td align="right" class="rptGrand"><?php echo (($ncreditTot > 0) ? number_format($ncreditTot,2) : '');?></td>
  </tr>

<?php
			$ndebitTot  = 0;
			$ncreditTot  = 0;

	}

			$ndebitTot = $ndebitTot + $row['ndebit'];
			$ncreditTot = $ncreditTot + $row['ncredit'];

?>
  <tr>
    <td><?php echo $acctno;?></td>
    <td><?php echo $ctitle;?></td>
    <td align="right"><?php echo (($row['ndebit'] > 0) ? number_format($row['ndebit'],2) : '');?></td>
    <td align="right"><?php echo (($row['ncredit'] > 0) ? number_format($row['ncredit'],2) : '');?></td>
  </tr>
<?php 

	
	}
?>

  <tr>
    <td colspan="2" class="rptGrand">&nbsp;</td>
    <td align="right" class="rptGrand"><?php echo (($ndebitTot > 0) ? number_format($ndebitTot,2) : '');?></td>
    <td align="right" class="rptGrand"><?php echo (($ncreditTot > 0) ? number_format($ncreditTot,2) : '');?></td>
  </tr>

</table>

</body>
</html>