<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "InvSum.php";


ini_set('MAX_EXECUTION_TIME', 900);


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
$totcost = 0;
$totretail = 0;

$cMonth = $_REQUEST['selm'];
$cYear = $_REQUEST['sely'];		
$date01 = $cYear."-".$cMonth."-01";

		$monthPrev  = floatval($cMonth)-1;
        $monthNum  = floatval($cMonth);

$date = date("Y-m-t", strtotime($date01));

echo $date;

function chkprice($citemno,$date){
	global $con;
	global $company;
	global $totcost;
	global $totretail;
	
	$totcost = 0;
	
	$sql = "select A.dcutdate, A.ddate, A.ncost  
			from 
			(
				Select dcutdate, ddate, ncost 
				from tblinvin 
				where compcode='$company' and ctranno not in (Select ctranno from receive) and citemno='$citemno'
				
				UNION ALL
				
				Select b.dreceived as dcutdate, b.ddate, a.ncost
			 	from receive_t a left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno
				where a.compcode='$company' and b.dreceived <= '$date' and a.citemno='$citemno'
			) A order by dcutdate desc, ddate desc LIMIT 1";

	$result0=mysqli_query($con,$sql);
	
	while($rowchk = mysqli_fetch_array($result0, MYSQLI_ASSOC))
	{
		
			$totcost = $rowchk['ncost'];
		
	}
	
	
	$sqlchkpricing = mysqli_query($con,"Select cpricetype, nmarkup from items where compcode='$company' and cpartno='$citemno'");
	if (mysqli_num_rows($sqlchkpricing)!=0) {
		$rowcostinvin = mysqli_fetch_assoc($sqlchkpricing);
		
		$grandret = 0;
		
		if($rowcostinvin["cpricetype"]=="MU"){
			$qtyMU = $rowcostinvin["nmarkup"];
			$totretail = (float)$totcost + ((float)$totcost*((float)$qtyMU/100));
		}
	}


}

function chkpriceave($citemno,$date1,$date2){
	global $con;
	global $company;
	global $totcost;
	global $totretail;
	
	$totcost = 0;
	
	$sql = "select A.dcutdate, A.ddate, A.ncost  
			from 
			(
				Select dcutdate, ddate, ncost 
				from tblinvin 
				where compcode='$company' and ctranno not in (Select ctranno from receive) and citemno='$citemno'
				
				UNION ALL
				
				Select b.dreceived as dcutdate, b.ddate, a.ncost
			 	from receive_t a left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno
				where a.compcode='$company' and b.dreceived between '$date1' and '$date2' and a.citemno='$citemno'
			) A order by dcutdate desc, ddate desc LIMIT 1";

	$result0=mysqli_query($con,$sql);
	
	while($rowchk = mysqli_fetch_array($result0, MYSQLI_ASSOC))
	{
		
			$totcost = $rowchk['ncost'];
		
	}
	
	
	$sqlchkpricing = mysqli_query($con,"Select cpricetype, nmarkup from items where compcode='$company' and cpartno='$citemno'");
	if (mysqli_num_rows($sqlchkpricing)!=0) {
		$rowcostinvin = mysqli_fetch_assoc($sqlchkpricing);
		
		$grandret = 0;
		
		if($rowcostinvin["cpricetype"]=="MU"){
			$qtyMU = $rowcostinvin["nmarkup"];
			$totretail = (float)$totcost + ((float)$totcost*((float)$qtyMU/100));
		}
	}


}


function chkqty($tbl1,$tbl2,$dtecol,$citmno){
	global $con;
	global $cMonth;
	global $cYear;
	global $monthPrev;
	global $monthNum;
	global $totqty;
	
	$totqty = 0;
	
	$sql = "select a.citemno, sum(a.nqty*nfactor) as nqty
	from ".$tbl1." a 
	left join ".$tbl2." b on a.compcode=b.compcode and a.ctranno = b.ctranno 
	where a.compcode='001' and MONTH(b.".$dtecol.") = $monthNum and YEAR(b.".$dtecol.") = $cYear and a.citemno='$citmno'
	and b.lcancelled=0
	Group by a.citemno";
//echo $sql."<br>";
	$result0=mysqli_query($con,$sql);
	
	if (mysqli_num_rows($result0)!=0) {
		while($rowchk = mysqli_fetch_array($result0, MYSQLI_ASSOC))
		{
				$totqty = $rowchk['nqty'];		
	
		}
	}
	else{
		$totqty = 0;
	}

} 

$totbal = 0;

function chkbal($citmno){
	global $con;
	global $cMonth;
	global $cYear;
	global $monthPrev;
	global $monthNum;
	global $totbal;
	
	$totbal = 0;
	
	$sql2 = "select a.citemno, sum(a.nactual) as nbal
	from adjustments_t a 
	left join adjustments b on a.compcode=b.compcode and a.ctrancode = b.ctrancode 
	where a.compcode='001' and b.dmonth = '$monthPrev' and b.dyear = '$cYear' and a.citemno='$citmno' and b.lapproved=1
	Group by a.citemno";

	//echo $sql2."<br>";
	$result2=mysqli_query($con,$sql2);
	
	if (mysqli_num_rows($result2)!=0) {
		while($rowchk2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
				$totbal = $rowchk2['nbal'];		
	
		}
	}
	else{
		$totbal = 0;
	}

}


?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inventory Summary</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>Inventory Summary</h2>
<h3>For <?php         $dateObj   = DateTime::createFromFormat('!m', $monthNum);
        $monthName = $dateObj->format('F'); // March
        
        echo $monthName . " " . $cYear;
		
		if(strlen($monthPrev)==1){
			$monthPrev = "0".$monthPrev;
		}
 ?></h3><br>
</center>

<table width="100%" border="0" align="center" cellpadding="3">
  <tr>
    <th rowspan="2">Classification</th>
    <th colspan="2" rowspan="2" style="text-align:center;">Product</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">UOM</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">Beg</th>
    <th colspan="2" style="text-align:center; border-right:1px solid">Qty In</th>
    <th colspan="2" style="text-align:center; border-right:1px solid">Qty Out</th>
    <th style="text-align:center; border-right:1px solid">Theo</th>
    <!--<th style="text-align:center; border-right:1px solid">Actual</th>-->
    <th rowspan="2" style="text-align:center; border-right:1px solid">Ave Cost/Unit</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">Ave Retail/Unit</th>
  </tr>
  <tr>
    <th style="text-align:center; border-right:1px solid">Purchases</th>
    <th style="text-align:center; border-right:1px solid">Sales Returns</th>
    <th style="text-align:center; border-right:1px solid">Sales</th>
    <th style="text-align:center; border-right:1px solid">Purchase Returns</th>
    <th style="text-align:center; border-right:1px solid">Ending Inv.</th>
  </tr>
  <?php

//$date1 = date_format(date_create($_POST["date1"]),"Y-m-d");

$sql = "Select B.cpartno as citemno, B.citemdesc, B.cunit, B.cclass, C.cdesc
		from items B
		left join groupings C on B.compcode=C.compcode and B.cclass=C.ccode and C.ctype='ITEMCLS'
		order by B.cclass, B.citemdesc";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$class="";
	$classval="";
	$classcode="";
	$totPrice=0;	
	$totCost=0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		chkqty('receive_t','receive','dreceived',$row['citemno']);
			$nrrin = $totqty;
	
		chkqty('salesreturn_t','salesreturn','dreceived',$row['citemno']);
			$nsretin = $totqty;
		
		chkqty('sales_t','sales','dcutdate',$row['citemno']);
			$nsalesout = $totqty;
		
		chkqty('purchreturn_t','purchreturn','dreturned',$row['citemno']);
			$npretout = $totqty;

		chkbal($row['citemno']);
			$nbal = $totbal;

	if(floatval($nbal) == 0 && floatval($nrrin) == 0 && floatval($nsretin) == 0 && floatval($nsalesout) == 0 && floatval($npretout) == 0){
	
	}
	else{
				
	
	$nqty =  (floatval($nbal) + floatval($nrrin) + floatval($nsretin)) - floatval($nsalesout) - floatval($npretout);
		if(floatval($nqty)>=1){
			
			chkpriceave($row['citemno'],$date01,$date);
			
				$nretprice = $totretail * $nqty;
				$ncosprice = $totcost * $nqty;

		}
		else{
				$nretprice = 0;
				$ncosprice = 0;

		}
		
		if($class!=$row['cclass']){
			$classval=$row['cdesc'];
			$classcode="class='rpthead'";
		}

	
	
?>
  <tr <?php echo $classcode;?>>
    <td><b><?php echo $classval;?></b></td>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['citemdesc'];?></td>
    <td style="border-right:1px solid"><?php echo $row['cunit'];?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($nbal)<>0) { 
			echo number_format($nbal,4); 
		} 
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($nrrin)<>0) { 
			echo number_format($nrrin,4); 
		} 
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($nsretin)<>0) { 
			echo number_format($nsretin,4); 
		} 
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($nsalesout)<>0) { 
			echo number_format($nsalesout,4); 
		} 
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($npretout)<>0) { 
			echo number_format($npretout,4); 
		} 
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($nqty)<>0) { 
			echo number_format($nqty,4); 
		} else{
			echo "-";
		}
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php 
		if (floatval($ncosprice)<>0) { 
			echo number_format($ncosprice,4); 
		} 
	?></td>
    <td align="right" style="text-align: right; border-right:1px solid"><?php
		if (floatval($nretprice)<>0) { 
			echo number_format($nretprice ,4); 
		} 
    
	?></td>
  </tr>
  <?php 
  
		$class=$row['cclass'];
		$classval="";
		$classcode="";


	}
		//$totCost = $totCost + $row['ncost'];
		//$totPrice = $totPrice + $row['nprice'];
	}
?>
</table>

</body>
</html>

