<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "ARAgeing.php";

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
$salestat = $_POST["selstat"];

$arrunpaidlist = array();
$arrwithapv = array();


$arrinvs = array();
$arrsupplist = array();
$arrsupps = array();


	//customers
	$arrsuppx = "";
	$arrterms = "";
	$sqlpay = "Select A.cempid as ccode, COALESCE(A.ctradename,A.cname) as cname, A.cterms, IFNULL(B.nallow,0) as nintval from customers A left join groupings B on A.compcode=B.compcode and A.cterms=B.ccode and B.ctype='TERMS' Where A.compcode='$company' and A.cempid = '".$ccodex ."'";
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
<title>AR Ageing</title>
</head>

<body style="padding:10px">
<center>
<h2><?php echo strtoupper($compname);  ?></h2>
<h2>AR Ageing Report</h2>
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

			$sql = "select * from ageing_days where compcode='$company' and cagetype='AR' order by id";
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
		//select all Sales Invoice
		$gtot = 0;
		$gtotftr = 0;

		//select all Sales Invoice
		/*if($salestat=="Trade"){
			$tbl = "sales";
		}elseif($salestat=="Non-Trade"){
			$tbl = "ntsales";
		}*/

		//if($salestat!==""){
			$sqlsuppinv = "Select A.ctranno, A.ngross, IFNULL(C.ncm,0) as ncm, IFNULL(C.ndm,0) as ndm, IFNULL(C.napplied,0) as napplied, A.ccode, A.dcutdate
			from ".$tbl." A 
			left JOIN
				(
					Select Z.csalesno, Sum(Z.napplied) as napplied, Sum(Z.ncm) as ncm, Sum(Z.ndm) as ndm
					From receipt_sales_t Z
					left join receipt X on Z.compcode=X.compcode and Z.ctranno=X.ctranno
					Where Z.compcode='$company' and X.lapproved=1 and X.ccode = '".$ccodex ."'
					Group by Z.csalesno
				) C on A.ctranno=C.csalesno
			Where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$date1', '%m/%d/%Y') and A.lapproved=1 and A.ccode='".$ccodex ."'";
		/*}else{
			$sqlsuppinv = "Select A.ctranno, A.ngross, IFNULL(C.ncm,0) as ncm, IFNULL(C.ndm,0) as ndm, IFNULL(C.napplied,0) as napplied, A.ccode, A.dcutdate
			from sales A 
			left JOIN
				(
					Select Z.csalesno, Sum(Z.napplied) as napplied, Sum(Z.ncm) as ncm, Sum(Z.ndm) as ndm
					From receipt_sales_t Z
					left join receipt X on Z.compcode=X.compcode and Z.ctranno=X.ctranno
					Where Z.compcode='$company' and X.lapproved=1 and X.ccode = '".$ccodex ."'
					Group by Z.csalesno
				) C on A.ctranno=C.csalesno
			Where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$date1', '%m/%d/%Y') and A.lapproved=1 and A.ccode='".$ccodex ."'
			UNION ALL
			Select A.ctranno, A.ngross, IFNULL(C.ncm,0) as ncm, IFNULL(C.ndm,0) as ndm, IFNULL(C.napplied,0) as napplied, A.ccode, A.dcutdate
			from ntsales A 
			left JOIN
				(
					Select Z.csalesno, Sum(Z.napplied) as napplied, Sum(Z.ncm) as ncm, Sum(Z.ndm) as ndm
					From receipt_sales_t Z
					left join receipt X on Z.compcode=X.compcode and Z.ctranno=X.ctranno
					Where Z.compcode='$company' and X.lapproved=1 and X.ccode = '".$ccodex ."'
					Group by Z.csalesno
				) C on A.ctranno=C.csalesno
			Where A.compcode='$company' and A.dcutdate <= STR_TO_DATE('$date1', '%m/%d/%Y') and A.lapproved=1 and A.ccode='".$ccodex ."'
			";
		}*/

		$result=mysqli_query($con,$sqlsuppinv);

		while($row999 = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{

			$sinet = floatval($row999['ngross']) + floatval($row999['ndm']) - floatval($row999['ncm']);
			$remain = $sinet - round(floatval($row999['napplied']),2);

			//if(round($sinet,2) > round(floatval($row999['napplied']),2)){
			if(round($remain,2)>0){
				$dategvn = $row999['dcutdate'];
				$cterms = $arrterms;
	?>
	<tr>
		<td nowrap><?=$row999['dcutdate']?></td>
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
							$nmtot = $nmtot + $remain;
						}else{

							if(floatval($row['fromdays']) > 0  && floatval($row['todays']) == 0){

								if($datediff >= floatval($row['fromdays'])){
								//	echo $datediff.": ".$row999['ngross']."<br>";
									$nmtot = $nmtot + $remain;
									
								}

							}else{
								if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
									//echo $datediff.": ".$row999['ngross']."<br>";
									$nmtot = $nmtot + $remain;
		
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


