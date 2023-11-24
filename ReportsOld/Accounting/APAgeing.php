<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "APAgeing.php";

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

	//get all RR and PO combo
	$arrporefs = array();
	$sqlsuppinv = "Select DISTINCT A.ctranno, A.creference, B.ladvancepay
	from receive_t A left join purchase B on A.compcode=B.compcode and A.creference=B.cpono
	Where A.compcode='$company'";
	$result=mysqli_query($con,$sqlsuppinv);

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrporefs[] = $row;
	}



	$arrinvs = array();
	$arrsupplist = array();
	
	//select all Suppliers Invoice
	$sqlsuppinv = "Select A.ctranno, A.ngross, A.ndue, A.npaidamount, A.ccode, A.dreceived, A.crefrr
	from suppinv A Where compcode='$company' and dreceived <= STR_TO_DATE('$date1', '%m/%d/%Y') and lapproved=1";
	$result=mysqli_query($con,$sqlsuppinv);

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrinvs[] = $row;

		$isok = "True";
		foreach($arrporefs as $rspox){
			if($row['crefrr']==$rspox['ctranno'] && $rspox['ladvancepay']==1){
				$isok = "False";
			}
		}

		if((floatval($row['ndue']) > floatval($row['npaidamount']) || floatval($row['ndue']) == 0) && $isok=="True"){
			$arrsupplist[] = $row['ccode'];
		}
	}

	//suppliers
	$arrsuppx = array();
	$sqlpay = "Select A.ccode, A.cname, A.cterms, IFNULL(B.nintval,0) as nintval from suppliers A left join groupings B on A.compcode=B.compcode and A.cterms=B.ccode and B.ctype='TERMS' Where A.compcode='$company' and A.ccode in ('".implode("','", $arrsupplist)."') Order by A.cname";
	$result=mysqli_query($con,$sqlpay);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrsuppx[] = array('ccode' => $row['ccode'], 'cname' => $row['cname'], 'terms' => $row['nintval']);
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
<h3>As Of <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> </h3>
</center>

<br><br>
<table width="100%" border="1" align="center" cellpadding = "3">
  <tr>
    <th>Supplier</th>
		
		<?php
			$arrdays = array();

			$arrtotperage = array();
			$sql = "select * from ageing_days where compcode='$company' and cagetype='AP' order by id";
			$result=mysqli_query($con,$sql);
			$cntr = 0;
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
					$arrdays[] = $row;
					$arrtotperage[$row['id']] = 0;
		?>
					<th width="150px"><?=$row['cdesc']?></th>
		<?php
			}
		?>

		<th nowrap>Grand Total</th>
  </tr>
 
	<?php
	$gtot = 0;
	$gtotftr = 0;
		foreach($arrsuppx as $rws0){
	?>
	<tr>
    <td nowrap onclick="funcset('<?=$rws0['ccode']?>','<?=$date1?>')" style="cursor: pointer "><?=$rws0['cname']?></td>
		<?php

			 foreach($arrdays as $row){
		?>
			<td nowrap align="right">
					<?php

						$nmtot = 0;
						
						foreach($arrinvs as $xr2){

							$isok = "True";
							foreach($arrporefs as $rspox){
								if($xr2['crefrr']==$rspox['ctranno'] && $rspox['ladvancepay']==1){
									$isok = "False";
								}
							}

								if($xr2['ccode']==$rws0['ccode'] && $isok=="True"){
									if(floatval($xr2['ndue']) > floatval($xr2['npaidamount']) || floatval($xr2['ndue']) == 0){

										$dategvn = $xr2['dreceived'];
										$cterms = $rws0['terms'];

										$your_date = date('Y-m-d', strtotime($dategvn. " + {$cterms} days"));


										$now = time(); // or your date as well $rws0['terms']
										$datediff = $now - strtotime($your_date);

										$datediff = round($datediff / (60 * 60 * 24));

										if($datediff < 0 && floatval($row['fromdays']) == 0  && floatval($row['todays']) == 0){
											$nmtot = $nmtot + floatval($xr2['ngross']);
										}else{

											if(floatval($row['fromdays']) > 0  && floatval($row['todays']) == 0){

												if($datediff >= floatval($row['fromdays'])){
					
													$nmtot = $nmtot + floatval($xr2['ngross']);
													
												}
					
											}else{
												if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
													//echo $datediff.": ".$xr2['ngross']."<br>";
													$nmtot = $nmtot + floatval($xr2['ngross']);
						
												}
											}

										}
							
										

									//	if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
											//echo $datediff.": ".$xr2['ngross']."<br>";
									//		$nmtot = $nmtot + floatval($xr2['ngross']);
									//	}

										
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
			<td nowrap align="right"><?=number_format($gtot,2);?></td>
	</tr>
	<?php
			$gtotftr = $gtotftr + $gtot; 
			$gtot = 0; 
		}
	?>

	<!-- TOTALS -->

	<tr>
    <td nowrap><b>TOTAL: </b></td>

		<?php
			foreach($arrdays as $row){
		?>
			<td nowrap align="right"><b><?=number_format($arrtotperage[$row['id']],2);?></b></td>
		<?php
			}
		?>

		
		<td nowrap align="right"><b> <?=number_format($gtotftr,2);?> </b></td>
	</tr>
</table>

<form action="APAgeing_Det.php" name="frmdet" id="frmdet" target="_blank" method="POST">
	<input type="hidden" name="ccode" id="ccode" value="">
	<input type="hidden" name="date1" id="date1" value="">
</body>
</html>

<script>
	function funcset(xcode, xdte){

		document.getElementById("ccode").value = xcode;
		document.getElementById("date1").value = xdte;
	
		document.getElementById("frmdet").submit(); 
	}
	</script>