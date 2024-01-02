<?php
if(!isset($_SESSION)){
session_start();
}
include('../../Connection/connection_string.php');
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
<title>PO Price Monitoring</title>

<style>
	table, th, td {
		white-space: nowrap !important;
	}

	.text-center { text-align:center; }
</style>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>PO Price Monitoring</h2>
<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3><br>
</center>

<br><br>
<table width="100%" border="0" align="center" cellpadding="5px">
	<thead>
		
		<tr>
			<th rowspan="2">Item Code</th>
			<th rowspan="2">Item Desc</th>
			<th rowspan="2" class="text-center">UOM</th>
			<th colspan="3" class="text-center">Previous</th>
			<th colspan="3" class="text-center">Present</th>
			<th rowspan="2" class="text-center">Status</th>
			<th rowspan="2" class="text-center">%</th>
		</tr>

		<tr>
			<th class="text-center">Supplier</th>
			<th class="text-center">Price</th>
			<th class="text-center">PO Date</th>
			<th class="text-center">Supplier</th>
			<th class="text-center">Price</th>
			<th class="text-center">PO Date</th>
		</tr>
	</thead>
  
<?php

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$datefil = $_POST["seltype"]; 

$selpost = $_POST["selpost"]; 

$qrypost = "";
if($selpost==1){
	$qrypost = " and b.lapproved = 1";
}

$sql = "select a.cpono as ctranno, b.".$datefil." as ddate, b.ccode, c.cname, a.nident, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, d.ctype, e.cdesc as typedesc, DATE(b.ddate) as PODate
From purchase_t a
left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
left join groupings e on d.compcode=e.compcode and d.ctype=e.ccode and e.ctype='ITEMTYP'
where a.compcode='".$company."' and b.lvoid = 0 and DATE(b.".$datefil.") between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')".$qrypost."
order by d.ctype, a.citemno, b.".$datefil." DESC";

//echo $sql;

$result=mysqli_query($con,$sql);

	$rowxsx = array();
	$itmslist = array();
	$itmcode = "";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))	
	{
		$rowxsx[] = $row;

		if($itmcode!==$row['citemno']){
			$itmslist[] = array('citemno' => $row['citemno'], 'cdesc' => $row['citemdesc'], 'cunit' => $row['cunit'], 'ctype' => $row['ctype'], 'typedesc' => $row['typedesc']);

			$itmcode = $row['citemno'];
		}
		
	}

	
	$classcode="";
	$classdesc="";
	$TOTPOAmt=0;	
	$TOTSIAmt=0;
	$ngross = 0;
	foreach($itmslist as $row)
	{

		$cntr = 0;
		$Supp1 = "";
		$Price1 = "";
		$Date1 = "";

		$Supp2 = "";
		$Price2 = "";
		$Date2 = "";
		foreach($rowxsx as $xxrow){
			if($xxrow['citemno']==$row['citemno']){
				$cntr++;

				if($cntr==1){
					$Supp1 = $xxrow['cname'];
					$Price1 = $xxrow['nprice'];
					$Date1 = $xxrow['PODate'];
				}elseif($cntr==2){
					$Supp2 = $xxrow['cname'];
					$Price2 = $xxrow['nprice'];
					$Date2 = $xxrow['PODate'];

					break;
				}
			}

		}

		if(($Price1!=="" && $Price2!=="") && $Price1!==$Price2){

			if($classcode!==$row['ctype']){
				$classcode=$row['ctype'];
				$classdesc=$row['typedesc'];
	
				echo "<tr colspan='10'><td><b>".$classcode.": ".$classdesc."</b></td>";
	
			}


?>  
  <tr>
    <td><?php echo $row['citemno'];?></td>
    <td><?php echo $row['cdesc'];?></td>
    <td><?php echo $row['cunit'];?></td>
    <td><?=$Supp1?></td>
		<td align="right"><?=number_format($Price1,2)?></td>
		<td class="text-center"><?=$Date1?></td>
		<td><?=$Supp2?></td>
		<td align="right"><?=number_format($Price2,2)?></td>
		<td class="text-center"><?=$Date2?></td>
		<td class="text-center">
			<?php
				if(floatval($Price1) > floatval($Price2)){
					echo "DEC";
				}else{
					echo "INC";
				}
			?>
		</td>

		<td class="text-center">
			<?php
				if(floatval($Price1) > floatval($Price2)){
					$Decrease = floatval($Price1) - floatval($Price2);
					$Decrease = ($Decrease/ floatval($Price1)) * 100;

					echo number_format($Decrease,2)."%";
				}else{
					$Increase = floatval($Price2) - floatval($Price1);
					$Increase = ($Increase/ floatval($Price2)) * 100;

					echo number_format($Increase,2)."%";
				}
			?>
		</td>

  </tr>
<?php 
		}
		}
?>

    
</table>

</body>
</html>