<?php
	if(!isset($_SESSION)){
	session_start();
	}
	$_SESSION['pageid'] = "IncomeStatement.php";

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

	$dteyr = $_POST["selyr"];

	$date1 = "01/01/".$dteyr;
	$date2 = "12/31/".$dteyr;

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Income Statement' ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}

	//glactivity
		$arrallwithbal = array();
		$sql = "Select MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
				From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
				where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
				and B.cFinGroup = 'Income Statement'
				Group By MONTH(ddate), A.acctno, B.cacctdesc
				Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
				Order By A.acctno, MONTH(ddate)";

		$result=mysqli_query($con,$sql);

		$darray = array();
		$months = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$darray[] = $row;
			$arrallwithbal[] = $row['acctno'];
			$months[] = $row['dmonth'];
			//echo $row['acctno']."<br>";
			getparent($row['acctno']);

		}

		$hdr_months = array_unique($months);
		asort($hdr_months);

		function getparent($cacctno){
			global $allaccounts;
			global $arrallwithbal;
			
			foreach($allaccounts as $zx0){

				if($zx0['cacctid']==$cacctno){
					//echo $zx0['cacctid']."".$zx0['mainacct']."<br><br>";
					$arrallwithbal[] = $zx0['mainacct'];
					getparent($zx0['mainacct']);
				}
				
			}
		}
	//end glactivity

	//echo "<pre>";
	//print_r(print_r($arrallwithbal));
	//echo "</pre>";

	//sort accounts .. tree mode
	$mainarray = array();
	$dacctarraylist = array();
	$result=mysqli_query($con,"SELECT A.ccategory, A.cacctno, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.compcode='$company' and A.cFinGroup='Income Statement' and A.cacctid in ('".implode("','", $arrallwithbal)."') ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$dacctarraylist[] = $row;
	}

//echo "<pre>";
//print_r($dacctarraylist);
//echo "</pre>";

	foreach($dacctarraylist as $rs1){
		if(intval($rs1['nlevel'])==1){
			$mainarray[] = array('ccategory' => $rs1['ccategory'], 'cacctid' => $rs1['cacctid'], 'cacctdesc' => $rs1['cacctdesc'], 'ctype' => $rs1['ctype'], 'nlevel' => $rs1['nlevel'], 'mainacct' => $rs1['mainacct']);
			if($rs1['ctype']=="General"){
				getchild($rs1['cacctid'], $rs1['nlevel']);
			}
		}
	}

	function getchild($acctcode, $nlevel){
		global $dacctarraylist;
		global $mainarray;

		foreach($dacctarraylist as $rsz){
			if($rsz['mainacct']==$acctcode){
				$mainarray[] = array('ccategory' => $rsz['ccategory'], 'cacctid' => $rsz['cacctid'], 'cacctdesc' => $rsz['cacctdesc'], 'ctype' => $rsz['ctype'], 'nlevel' => $rsz['nlevel'], 'mainacct' => $rsz['mainacct']);

				if($rsz['ctype']=="General"){
					getchild($rsz['cacctid'], $rsz['nlevel']);
				}
			}
		}
	}

	function gettotal($acctid, $xctype, $xmo){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid && $rsp['dmonth']==$xmo){

				switch ($xctype) {
					case "REVENUE":
						$xtot = floatval($rsp['ncredit']) - floatval($rsp['ndebit']);
						break;
					case "COST OF SALES":
						$xtot = floatval($rsp['ndebit']) - floatval($rsp['ncredit']);
						break;
					case "EXPENSES":
						$xtot = floatval($rsp['ndebit']) - floatval($rsp['ncredit']);
						break;
					}

				break;

			}
		}

		return $xtot;
	}

	function getpads($xlevl){

		$GENxyz = intval($xlevl);

		$GENxyz0 = 0;	

		switch ($GENxyz) {
			case 1:
				$GENxyz0 = 0;
				break;
			case 2:
				$GENxyz0 = 10;
				break;
			case 3:
				$GENxyz0 = 25;
				break;
			case 4:
				$GENxyz0 = 40;
				break;
			case 5:
				$GENxyz0 = 65;
				break;
			default:
				$GENxyz0 = 0;
		}

		return $GENxyz0;
	}

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css?x=<?=time()?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Profit &amp; Lost Statement</title>

	<style>
			tr:hover {
				background-color: gainsboro;
			}
			@media print {
				.my-table {
					width: 100% !important;
				}
			}
	</style>
</head>

<body style="padding:10px">

	<h3><b>Company: <?=strtoupper($compname);?></b></h3>	
	<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
	<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
	<h3><b>Profit &amp; Lost Statement</b></h3>
	<h3>For the Year <?=$dteyr?></h3>

<br><br>
<table width="100%" border="0" align="center" cellpadding="3" class="my-table">
  <tr>

    <th style="text-align:center" width="100px">Account No. </th>
    <th style="text-align:center">Account Name</th>
    <?php
			foreach($hdr_months as $rx){
				$monthNum  = $rx;
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F');
		?>
    <th style="text-align:center"><?=$monthName?></th>
		<?php
			}
		?>
  </tr>
 
 <?php

 //echo "<pre>";
 //print_r($mainarray);
 //echo "</pre>";

	$arrlvl = array();
	$arrlvldsc = array();

	foreach($hdr_months as $rxzm){

		$profitRevn[$rxzm] = 0;
		$profitCost[$rxzm] = 0;
		$BPROFITzc0[$rxzm] = 0;
		$BPEXPzc0[$rxzm] = 0;

		$arrlvlamt[0][$rxzm] = 0;
		$arrlvlamt[1][$rxzm] = 0;
		$arrlvlamt[2][$rxzm] = 0;
		$arrlvlamt[3][$rxzm] = 0;
		$arrlvlamt[4][$rxzm] = 0;
		$arrlvlamt[5][$rxzm] = 0;
	}

	$arrlvlcnt = 0;

	$ccate = $mainarray[0]['ccategory'];

	echo "<tr><td colspan='3'><b>".$ccate."</b></td></tr>";

	$csubcate = "";

	$nlevel = 0;
	foreach($mainarray as $row)
	{

		if(intval($row['nlevel']) < intval($arrlvlcnt)){

			for($x=intval($row['nlevel']); $x<intval($arrlvlcnt); $x++){
				
				if($x!=intval($row['nlevel'])){

					$GENxyz1 = getpads($x);

					echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[$x]. "</b></td>";

					foreach($hdr_months as $rxzm){

						$toformat = (floatval($arrlvlamt[intval($x)][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[intval($x)][$rxzm]),2).")": number_format($arrlvlamt[intval($x)][$rxzm],2);

						echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";

						$arrlvlamt[intval($x)][$rxzm] = 0;
					}

					echo "</tr>";
				}

			}

			$GENxyz1 = getpads($row['nlevel']);

			echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($row['nlevel'])]."</b></td>";

				foreach($hdr_months as $rxzm){

					$toformat = (floatval($arrlvlamt[intval($row['nlevel'])][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[intval($row['nlevel'])][$rxzm]),2).")": number_format($arrlvlamt[intval($row['nlevel'])][$rxzm],2);

					echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";

					$arrlvlamt[intval($row['nlevel'])][$rxzm] = 0;
				}

			echo "</tr>";
		}

		if($row['ctype']=="General"){

			$arrlvl[$row['nlevel']] = $row['cacctid']; 
			$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 

		}
		
		if($ccate!==$row['ccategory']){
	

			echo "<tr><td colspan='2' style='border-bottom-style: double; border-top: 2px solid #000;'><b>TOTAL ".$ccate."</b></td>";

			foreach($hdr_months as $rxzm){

				$toformat = (floatval($arrlvlamt[0][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[0][$rxzm]),2).")": number_format($arrlvlamt[0][$rxzm],2);

				echo "<td align='right' style='border-bottom-style: double; border-top: 2px solid #000; padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";

				if($ccate=="REVENUE"){
					$profitRevn[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
				}
	
				if($ccate=="COST OF SALES"){				
					 $profitCost[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
				}
	
				$arrlvlamt[0][$rxzm] = 0;
	
			}

			echo "</tr>";



			if($row['ccategory']=="EXPENSES"){

					echo "<tr><td colspan='2' style='padding-top:10px'><b>GROSS PROFIT</b></td>";
					
					foreach($hdr_months as $rxzm){

						$BPROFITzc0[$rxzm] = floatval($profitRevn[$rxzm]) - floatval($profitCost[$rxzm]);
						$donetwo = ($BPROFITzc0[$rxzm]<0) ? "(".number_format(abs($BPROFITzc0[$rxzm]),2).")" : number_format(($BPROFITzc0[$rxzm]),2);

						echo "<td align='right' style='border-bottom: 1px solid #000; padding-top:10px; padding-right: 20px; padding-left: 20px;'><b>".$donetwo."</b></td>";
					}

					echo "</tr>";

			}

			echo "<tr><td colspan='3' style='padding-top: 20px'><b>".$row['ccategory']."</b></td></tr>";
		}
	
		
		$GENxyz1 = getpads($row['nlevel']);
?>
   <tr>

    <td onclick="funcset('<?=$row['cacctid']?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer">
			<?php
				if($row['ctype']=="General"){
					//echo "<b>".$row['cacctid']."</b>";
				}else{
					echo $row['cacctid'];
				}
			?>
		</td>
    <td onclick="funcset('<?=$row['cacctid']?>','<?=$date1?>','<?=$date2?>')" style="cursor: pointer; text-indent:<?=$GENxyz1?>px " nowrap>
			<?php
				if($row['ctype']=="General"){
					echo "<b>".$row['cacctdesc']."</b>";
				}else{
					echo $row['cacctdesc'];
				}
			?>
		</td>
  	<?php
			foreach($hdr_months as $rxzm){
		?>
  	<td style="text-align:right; padding-right: 20px; padding-left: 20px ">
				<?php
					if($row['ctype']=="Details"){

						$xcvb = gettotal($row['cacctid'], $row['ccategory'], $rxzm);

						for ($x = 0; $x < intval($row['nlevel']); $x++) {
							$arrlvlamt[$x][$rxzm] = $arrlvlamt[$x][$rxzm] + $xcvb;
						} 

						if(floatval($xcvb) > 0){
							echo number_format($xcvb,2); 

							$xmain = intval($row['nlevel']) - 1;
							
						}elseif(floatval($xcvb) < 0){
							echo "(".number_format(abs($xcvb),2).")";
						}else{
							echo "-";
						}
					}
				?>
		</td>
		<?php
				}
		?>
  </tr>
<?php

		$ccate = $row['ccategory'];
		$arrlvlcnt = $row['nlevel'];

	}

	if(intval($arrlvlcnt) !== 1){

		$GENxyz1 = getpads($arrlvlcnt-1);

		echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($arrlvlcnt)-1]."</b></td>";

		foreach($hdr_months as $rxzm){

			$toformat = (floatval($arrlvlamt[intval($arrlvlcnt)-1][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[intval($arrlvlcnt)-1][$rxzm]),2).")": number_format($arrlvlamt[intval($arrlvlcnt)-1][$rxzm],2);

			echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid; padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";
		}

		echo "</tr>";

	}

		echo "<tr><td colspan='2' style='border-bottom: 2px solid #000; border-top: 1px solid #000;'><b>TOTAL ".$ccate."</b></td>";

		foreach($hdr_months as $rxzm){

			$donetwo = ($arrlvlamt[0][$rxzm]<0) ? "(".number_format(abs($arrlvlamt[0][$rxzm]),2).")" : number_format(($arrlvlamt[0][$rxzm]),2);

			$toformat = (floatval($arrlvlamt[intval($arrlvlcnt)-1][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[intval($arrlvlcnt)-1][$rxzm]),2).")": number_format($arrlvlamt[intval($arrlvlcnt)-1][$rxzm],2);

			echo "<td align='right' style='border-bottom: 2px solid #000; border-top: 1px solid #000; ; padding-right: 20px; padding-left: 20px;'><b>".$donetwo."</b></td>";

			if($ccate=="EXPENSES"){
				$BPEXPzc0[$rxzm] = floatval($arrlvlamt[0][$rxzm]);
			}
		
			$xctot[$rxzm] = $BPROFITzc0[$rxzm]-$BPEXPzc0[$rxzm];
			$xctotax[$rxzm] = 0;
			$xctotaxaftr[$rxzm] = 0;
		}

		echo "</tr>";

?>

<tr><td colspan='3' style='padding-top: 10px'>&nbsp;</td></tr>
<tr>
	<td colspan='2' style='border-bottom-style: double; border-top: 1px solid #000;'>
		<b>NET INCOME/(LOSS) BEFORE TAX</b>
	</td>
	<?php
		foreach($hdr_months as $rxzm){
	?>
		<td align='right' style='border-bottom-style: double; border-top: 1px solid #000;'> 
			<b><?=($xctot[$rxzm]<0) ? "(".number_format(abs($xctot[$rxzm]),2).")" : number_format(($xctot[$rxzm]),2)?></b>
		</td>
	<?php
		}
	?>
	
</tr>


<tr>
	<td colspan='2'>
		<b>PROVISION FOR INCOME TAX <?=$_REQUEST['ITper']."%"?></b> 
	</td>
	<?php
		foreach($hdr_months as $rxzm){

			if(($xctot[$rxzm]) > 0) {
				$xctotax[$rxzm] = $xctot[$rxzm] * (intval($_REQUEST['ITper'])/100);
				$xctotaxaftr[$rxzm] = $xctot[$rxzm] - $xctotax[$rxzm];
	?>
		<td align='right'> 
			<b><?=($xctotax[$rxzm]<0) ? "(".number_format(abs($xctotax[$rxzm]),2).")" : number_format(($xctotax[$rxzm]),2)?></b>
		</td>
	<?php
			}else{
				echo "<td align='right'>-</td>";
			}
		}
	?>
</tr>

<tr>
	<td colspan='2'>
		<b>PROVISION FOR MCIT (<?=$_REQUEST['MCITper']."%"?> OF GROSS INCOME)</b>
	</td>
	<?php
		foreach($hdr_months as $rxzm){

			if(($xctot[$rxzm]) < 0) {
				$xctotax[$rxzm] = $BPROFITzc0[$rxzm] * (intval($_REQUEST['MCITper'])/100);
				$xctotaxaftr[$rxzm] = $xctot[$rxzm] - $xctotax[$rxzm];
	?>
		<td align='right'> 
			<b><?=($xctotax[$rxzm]<0) ? "(".number_format(abs($xctotax[$rxzm]),2).")" : number_format(($xctotax[$rxzm]),2)?></b>
		</td>
	<?php
			}else{
				echo "<td align='right'>-</td>";
			}
		}
	?>
</tr>

<tr>
	<td colspan='2' style='border-bottom-style: double;'>
		<b>NET INCOME AFTER TAX</b>
	</td>
	<?php
		foreach($hdr_months as $rxzm){
	?>
		<td align='right' style='border-bottom-style: double;'> 
			<b><?=($xctotaxaftr[$rxzm]<0) ? "(".number_format(abs($xctotaxaftr[$rxzm]),2).")" : number_format(($xctotaxaftr[$rxzm]),2)?></b>
		</td>
	<?php
		}
	?>
</tr>
 
</table>

<form action="TBal_Det.php" name="frmdet" id="frmdet" target="_blank" method="POST">
	<input type="hidden" name="ccode" id="ccode" value="">
	<input type="hidden" name="date1" id="date1" value="">
	<input type="hidden" name="date2" id="date2" value="">
</form>

</body>
</html>

<script>
	function funcset(xcode, xdte1, xdte2){

		document.getElementById("ccode").value = xcode;
		document.getElementById("date1").value = xdte1;
		document.getElementById("date2").value = xdte2;
	
		document.getElementById("frmdet").submit(); 
	}
</script>