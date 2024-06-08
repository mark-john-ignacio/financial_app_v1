<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "APAgeing";

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
$ccodex = $_POST["ccode"];


$arrunpaidlist = array();
$arrwithapv = array();


$arrinvs = array();
$arrsupplist = array();
$arrsupps = array();


	//suppliers
	$arrsuppx = "";
	$arrterms = "";
	$sqlpay = "Select A.ccode, A.cname, A.cterms, IFNULL(B.nintval,0) as nintval from suppliers A left join groupings B on A.compcode=B.compcode and A.cterms=B.ccode and B.ctype='TERMS' Where A.compcode='$company' and A.ccode = '".$ccodex ."'";
	$result=mysqli_query($con,$sqlpay);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrsuppx = $row['cname'];
		$arrterms = $row['nintval'];
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AP Ageing</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>AP Ageing Report</h2>
<h3>As Of <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?></h3>
<h3><?=$ccodex. " - ".$arrsuppx?>  (<?=$arrterms." days terms"?>)</h3>
</center>

<br><br>
<table width="100%" border="1" align="center" cellpadding = "3">
  <tr>
    <th>SI Date</th>
		<th>Due Date</th>
		<th>Trans No.</th>
		<?php
			$aarag = array();
			$arrtotperage = array();
			$sql = "select * from ageing_days where compcode='$company' and cagetype='AP' order by id";
			$result=mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$aarag[] = $row;
				$arrtotperage[$row['id']] = 0;
		?>
					<th width="150px"><?=$row['cdesc']?></th>
		<?php
				}
		?>
  </tr>
 
	<?php
		//select all Suppliers Invoice
		$gtot = 0;

		$sqlsuppinv = "Select A.ctranno, A.ngross, A.ndue, A.npaidamount, A.ccode, A.dreceived
		from suppinv A Where compcode='$company' and dreceived <= STR_TO_DATE('$date1', '%m/%d/%Y') and lapproved=1 and A.ccode='$ccodex'
		Order by A.ctranno";

		$result=mysqli_query($con,$sqlsuppinv);

		while($row999 = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{

			if(floatval($row999['ndue']) > floatval($row999['npaidamount']) || floatval($row999['ndue']) == 0){

				$dategvn = $row999['dreceived'];
				$cterms = $arrterms;
	?>
	<tr>
		<td nowrap><?=$row999['dreceived']?></td>
		<td nowrap>
			<?=date('Y-m-d', strtotime($dategvn. " + {$cterms} days"))?>
		</td>
    <td nowrap><?=$row999['ctranno']?></td>
		<?php
			 $arrdays = array();

			foreach($aarag as $row)
			{
		?>
			<td nowrap align="right">
					<?php

						$nmtot = 0;

						$your_date = date('Y-m-d', strtotime($dategvn. " + {$cterms} days"));


						$now = time(); // or your date as well $rws0['terms']
						$datediff = $now - strtotime($your_date);

						$datediff = round($datediff / (60 * 60 * 24));

						if($datediff < 0 && floatval($row['fromdays']) == 0  && floatval($row['todays']) == 0){
							$nmtot = $nmtot + floatval($row999['ngross']);
						}else{

							if(floatval($row['fromdays']) > 0  && floatval($row['todays']) == 0){

								if($datediff >= floatval($row['fromdays'])){
								//	echo $datediff.": ".$row999['ngross']."<br>";
									$nmtot = $nmtot + floatval($row999['ngross']);
									
								}

							}else{
								if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
									//echo $datediff.": ".$row999['ngross']."<br>";
									$nmtot = $nmtot + floatval($row999['ngross']);
		
								}
							}

						}
						

						if($nmtot > 0){
							echo number_format($nmtot,2);

							$arrtotperage[$row['id']] = $arrtotperage[$row['id']] + $nmtot;

							$gtot = $gtot + $nmtot;
						}
						
					?>


			</td>
		<?php
				}
		?>
	</tr>
	<?php

		}
	}
	?>


<!-- TOTALS -->

	<tr>
    <td nowrap colspan="3"><b>TOTAL: </b></td>

		<?php
			foreach($aarag as $row){
		?>
			<td nowrap align="right"><b><?=number_format($arrtotperage[$row['id']],2);?></b></td>
		<?php
			}
		?>

		
		<!--<td nowrap align="right"><b> <?//=number_format($gtotftr,2);?> </b></td>-->
	</tr>
</table>
