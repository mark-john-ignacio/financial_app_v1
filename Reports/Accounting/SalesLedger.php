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
		$compadd = $row['compadd'];
		$comptin = $row['comptin'];
	}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">	
	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
	<title>Sales Journal</title>
</head>

<body style="padding:10px">
<h4><b>Company: <?=strtoupper($compname);  ?></b></h4>
<h4><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h4>
<h4><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h4>
<h4><b>Kind of Book: SALES BOOK</b></h4>
<h4><b>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></b></h4>
<br>
<table class='table table-condensed' id='crjTable' border="0" align="center">
  <tr>
    <th>Date</th>
    <th>Invoice No.</th>
    <th>Name</th>
    <th>Account No.</th>
    <th>Account Title</th>
    <th>Debit</th>
    <th>Credit</th>
  </tr>
  
<?php

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.acctno, A.ctitle, A.ncredit, A.ndebit, A.lcancelled, A.lapproved
	FROM(
	select a.dcutdate, a.ctranno as csalesno, a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.lcancelled, a.lapproved
	From sales a
	left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
	where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lapproved=1 and a.lvoid=0
	) A order by A.dcutdate, A.csalesno, A.ndebit desc";


	$x = check_nt($_SESSION['companyid']);
    if($x==1){

		if($_POST["selNTy"]=="Non-Trade"){
			$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.acctno, A.ctitle, A.ncredit, A.ndebit, A.lcancelled, A.lapproved
			FROM(
			select a.dcutdate, a.ctranno as csalesno, a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.lcancelled, a.lapproved
			From ntsales a
			left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
			where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lapproved=1 and a.lvoid=0
			) A order by A.dcutdate, A.csalesno, A.ndebit desc";
		}elseif($_POST["selNTy"]==""){
			$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.acctno, A.ctitle, A.ncredit, A.ndebit, A.lcancelled, A.lapproved
			FROM(
			select a.dcutdate, a.ctranno as csalesno, a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.lcancelled, a.lapproved
			From sales a
			left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
			where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lapproved=1 and a.lvoid=0

			UNION ALL

			select a.dcutdate, a.ctranno as csalesno, a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.lcancelled, a.lapproved
			From ntsales a
			left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
			where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lapproved=1 and a.lvoid=0
			) A order by A.dcutdate, A.csalesno, A.ndebit desc";
		}

	}

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
		
		//if($salesno!=$row['csalesno']){
			$code = $row['ccode'];
			$name= $row['cname'];
			$invval = $row['csalesno'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		//}
		
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
    <!--<td><?//php echo $code;?></td>-->
    <td><?php echo $name;?></td>
    <td><?php echo $row['acctno'];?></td>
    <td><?php echo $row['ctitle'];?></td>
    <td align="right"><?php echo (($row['ndebit'] > 0) ? number_format($row['ndebit'],2) : '');?></td>
    <td align="right"><?php echo (($row['ncredit'] > 0) ? number_format($row['ncredit'],2) : '');?></td>
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

    <tr>
    	<td colspan="5" align="right"><b>TOTAL</b></td>
        <td style='text-align:right; border-top: 2px solid !important'><b><?php echo number_format($totDebit,2);?></b></td>
        <td style='text-align:right; border-top: 2px solid !important'><b><?php echo number_format($totCredit,2);?></b></td>
    </tr>
</table>

</body>
</html>