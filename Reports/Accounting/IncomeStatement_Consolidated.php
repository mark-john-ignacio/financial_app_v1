<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "IncomeStatement";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$arrcomps = array();
	$arrcompsname = array();
	
	$company = $_SESSION['companyid'];
	$sql = "select compcode, compname From company";
	$result=mysqli_query($con,$sql);
  	$rowcount=mysqli_num_rows($result);
	if($rowcount>0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrcomps[] = $row;
			$arrcompsname[] = $row['compname'];
		}
	}


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

	//getall accounts
	$allaccounts = array();
	$result=mysqli_query($con,"SELECT DISTINCT A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.cFinGroup='Income Statement' ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$allaccounts[] = $row;
	}

	$qrydte = "";
	if($_POST['seldte']==1){
		$date1 = $_POST["date1"];
		$date2 = $_POST["date2"];

		$qrydte = "A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')";
	}else{
		$dteyr = $_POST["selyr"];
		$qrydte = "YEAR(A.ddate) = '$dteyr'";	

		$date1 = "01/01/".$dteyr;
		$date2 = "12/31/".$dteyr;
	}

	//glactivity
		$arrallwithbal = array();
		$sql = "Select A.compcode, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
				From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
				where ".$qrydte." and IFNULL(B.cacctdesc,'') <> ''
				and B.cFinGroup = 'Income Statement'
				Group By A.compcode, A.acctno, B.cacctdesc
				Having sum(A.ndebit)<>0 or sum(A.ncredit)<>0
				Order By A.compcode, A.acctno";

		$result=mysqli_query($con,$sql);

		$darray = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$darray[] = $row;
			$arrallwithbal[] = $row['acctno'];
			//echo $row['acctno']."<br>";
			getparent($row['acctno']);

		}

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
	$result=mysqli_query($con,"SELECT DISTINCT A.ccategory, A.cacctid, A.cacctdesc, A.nlevel, A.mainacct, A.ctype FROM `accounts` A where A.cFinGroup='Income Statement' and A.cacctid in ('".implode("','", $arrallwithbal)."') ORDER BY CASE WHEN A.ccategory='REVENUE' THEN 1 WHEN A.ccategory='COST OF SALES' THEN 2 WHEN A.ccategory='EXPENSES' THEN 3 END, A.nlevel, A.cacctid");

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

	function gettotal($acctid, $xctype, $ccode){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid && $rsp['compcode']==$ccode){

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
<h3><b>Company: <?=implode(", ",$arrcompsname);  ?></b></h3>
<h3><b>Company Address: <?php echo strtoupper($compadd);  ?></b></h3>
<h3><b>Vat Registered Tin: <?php echo $comptin;  ?></b></h3>
<h3><b>Profit &amp; Lost Statement</b></h3>
<?php
	if($_POST['seldte']==1){
?>
	<h3>For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h3>
<?php
	}else{
?>
	<h3>For the Year <?=$_POST["selyr"]?></h3>
<?php
	}
?>


<?php
	if(count($mainarray) > 0){

?>
<br><br>
<table width="100%" border="0" align="center" cellpadding="3" class="my-table">
  <tr>

    <th style="text-align:center" width="100px">Account No. </th>
    <th style="text-align:center">Account Name</th>
	<?php
		foreach($arrcomps as $row){
			echo "<th style=\"text-align:right\">".$row['compname']."</th>";
		}
	?>
    <th style="text-align:right">Total</th>
  </tr>
 
 <?php

 //echo "<pre>";
 //print_r($mainarray);
 //echo "</pre>";


	$arrlvl = array();
	$arrlvldsc = array();
	$donetwo = array();
	$profitRevn =  array();
	$profitCost=  array();
	$BPROFITzc0 =  array();
	$BPEXPzc0 =  array();

	foreach($arrcomps as $comprow){
		$arrlvlamt[$comprow['compcode']."0"] = 0;
		$arrlvlamt[$comprow['compcode']."1"] = 0;
		$arrlvlamt[$comprow['compcode']."2"] = 0;
		$arrlvlamt[$comprow['compcode']."3"] = 0;
		$arrlvlamt[$comprow['compcode']."4"] = 0;
		$arrlvlamt[$comprow['compcode']."5"] = 0;

		$donetwo[$comprow['compcode']] = 0;
		$profitRevn[$comprow['compcode']] = 0;
		$profitCost[$comprow['compcode']] = 0;
		$BPROFITzc0[$comprow['compcode']] = 0;
		$BPEXPzc0[$comprow['compcode']] = 0;
	}

	$arrlvlcnt = 0;

	$ccate = $mainarray[0]['ccategory'];

	echo "<tr><td colspan='3'><b>".$ccate."</b></td></tr>";

	$csubcate = "";

	$nlevel = 0;
	foreach($mainarray as $row)
	{

		if(intval($row['nlevel']) < intval($arrlvlcnt)){

			$GENxyz1 = getpads($row['nlevel']);

			echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($row['nlevel'])]."</b></td>";
			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b>".number_format($arrlvlamt[$comprow['compcode'].intval($row['nlevel'])],2)."</b></td>";

				$mygtooth = $mygtooth + floatval($arrlvlamt[$comprow['compcode'].intval($row['nlevel'])]);
			}
			echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b> ".number_format($mygtooth,2)." </b</td></tr>";

			$arrlvlamt[$comprow['compcode'].intval($row['nlevel'])] = 0;
		}

		if($row['ctype']=="General"){

			$arrlvl[$row['nlevel']] = $row['cacctid']; 
			$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 

		}
		
		if($ccate!==$row['ccategory']){

			echo "<tr><td colspan='2' style='border-bottom-style: double; border-top: 2px solid #000;'><b>TOTAL ".$ccate."</b></td>";
			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				echo "<td align='right' style='border-bottom-style: double; border-top: 2px solid #000;'><b>".number_format($arrlvlamt[$comprow['compcode']."0"],2)."</b></td>";
			
				$mygtooth = $mygtooth + floatval($arrlvlamt[$comprow['compcode']."0"]);

				if($ccate=="REVENUE"){
					$profitRevn[$comprow['compcode']] = floatval($arrlvlamt[$comprow['compcode']."0"]);
				}

				if($ccate=="COST OF SALES"){				
					$profitCost[$comprow['compcode']] = floatval($arrlvlamt[$comprow['compcode']."0"]);
				}

				$arrlvlamt[$comprow['compcode']."0"] = 0;

			}
			echo "<td align='right' style='border-bottom-style: double; border-top: 2px solid #000;'><b> ".number_format($mygtooth,2)." </b</td></tr>";


			if($row['ccategory']=="EXPENSES"){
				echo "<tr><td colspan='2' style='padding-top:10px'><b>GROSS PROFIT</b></td>";
				$mygtooth = 0;
				foreach($arrcomps as $comprow){
					$BPROFITzc0[$comprow['compcode']] = floatval($profitRevn[$comprow['compcode']]) - floatval($profitCost[$comprow['compcode']]);
					$donetwo[$comprow['compcode']] = ($BPROFITzc0[$comprow['compcode']]<0) ? "(".number_format(abs($BPROFITzc0[$comprow['compcode']]),2).")" : number_format(($BPROFITzc0[$comprow['compcode']]),2);

					$mygtooth = $mygtooth + floatval($BPROFITzc0[$comprow['compcode']]);
					
					echo "<td align='right' style='border-bottom: 1px solid #000; padding-top:10px'><b>".$donetwo[$comprow['compcode']]."</b></td>";

					
				}
				echo "<td align='right' style='border-bottom: 1px solid #000; padding-top:10px'><b> ".number_format($mygtooth,2)." </b</td></tr>";
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
	$mygtooth = 0;
		foreach($arrcomps as $comprow){
	?>
  		<td style="text-align:right">
				<?php
					if($row['ctype']=="Details"){
						$xcvb = gettotal($row['cacctid'], $row['ccategory'], $comprow['compcode']);


						for ($x = 0; $x < intval($row['nlevel']); $x++) {
							$arrlvlamt[$comprow['compcode'].$x] = $arrlvlamt[$comprow['compcode'].$x] + $xcvb;
						}

						if(floatval($xcvb) > 0){
							echo number_format($xcvb,2);

							$xmain = intval($row['nlevel']) - 1;
							$mygtooth = $mygtooth + floatval($xcvb);
						}elseif(floatval($xcvb) < 0){
							echo "(".number_format(abs($xcvb),2).")";

							$mygtooth = $mygtooth + floatval($xcvb);

						}else{
							echo "-";
						}

						
					}
				?>
		</td>
	<?php
		}
	?>
		<td style="text-align:right"><b><?=($row['ctype']=="Details") ? number_format($mygtooth,2) : "";?></b></td>
  </tr>
<?php

		$ccate = $row['ccategory'];
		$arrlvlcnt = $row['nlevel'];

	}

	if(intval($arrlvlcnt) !== 1){

		$GENxyz1 = getpads($arrlvlcnt-1);

		echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($arrlvlcnt)-1]."</b></td>";
			$mygtooth = 0;
			foreach($arrcomps as $comprow){
				echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b>".number_format($arrlvlamt[$comprow['compcode'].(intval($arrlvlcnt)-1)],2)."</b></td>";

				$mygtooth = $mygtooth + $arrlvlamt[$comprow['compcode'].(intval($arrlvlcnt)-1)];
			}
		echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b> ".number_format($mygtooth,2)." </b></td></tr>";
	}

	$xctotsuperTotl = 0;
	$xctot = array();
	$xctotax = array();
	$xctotaxaftr = array();

	echo "<tr><td colspan='2' style='border-bottom: 2px solid #000; border-top: 1px solid #000;'><b>TOTAL ".$ccate."</b></td>";
	$mygtooth = 0;
	foreach($arrcomps as $comprow){

		$donetwo[$comprow['compcode']] = ($arrlvlamt[$comprow['compcode']."0"]<0) ? "(".number_format(abs($arrlvlamt[$comprow['compcode']."0"]),2).")" : number_format(($arrlvlamt[$comprow['compcode']."0"]),2);

		echo "<td align='right' style='border-bottom: 2px solid #000; border-top: 1px solid #000;'><b>".$donetwo[$comprow['compcode']]."</b></td>";

		$mygtooth = $mygtooth + $arrlvlamt[$comprow['compcode']."0"];

		if($ccate=="EXPENSES"){
			$BPEXPzc0[$comprow['compcode']] = floatval($arrlvlamt[$comprow['compcode']."0"]);
		}

		$xctot[$comprow['compcode']] = $BPROFITzc0[$comprow['compcode']] -$BPEXPzc0[$comprow['compcode']];
		$xctotax[$comprow['compcode']] = 0;
		$xctotaxaftr[$comprow['compcode']] = 0;

		$xctotsuperTotl = $xctotsuperTotl + $xctot[$comprow['compcode']];

	}

	echo "<td align='right' style='border-bottom: 2px solid #000; border-top: 1px solid #000;'><b> ".number_format($mygtooth,2)." </b></td></tr>";
?>

<tr><td colspan='3' style='padding-top: 10px'>&nbsp;</td></tr>
<tr>
	<td colspan='2' style='border-bottom-style: double; border-top: 1px solid #000;'>
		<b>NET INCOME/(LOSS) BEFORE TAX</b>
	</td>
	<?php
		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			$mygtooth = $mygtooth + $xctot[$comprow['compcode']];
	?>
	<td align='right' style='border-bottom-style: double; border-top: 1px solid #000;'> 
		<b><?=($xctot[$comprow['compcode']]<0) ? "(".number_format(abs($xctot[$comprow['compcode']]),2).")" : number_format(($xctot[$comprow['compcode']]),2)?></b>
	</td>
	<?php
		}
	?>
	<td align='right' style='border-bottom-style: double; border-top: 1px solid #000;'> 
		<b><?=(floatval($mygtooth)<0) ? "(".number_format(abs($mygtooth),2).")" : number_format($mygtooth,2)?></b>
	</td>
</tr>
<?php
	$isPrvo  = "False";
	$isProvmcit = "False";
	foreach($arrcomps as $comprow){
		if(($xctot[$comprow['compcode']]) > 0) {	
			$isPrvo  = "True";
		}

		if(($xctot[$comprow['compcode']]) < 0) {
			$isProvmcit = "True";
		}
	}

	if($isPrvo=="True") {			
?>
<tr>
	<td colspan='2'>
		<b>PROVISION FOR INCOME TAX <?=$_REQUEST['ITper']."%"?></b> 
	</td>
	<?php
		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			if(($xctot[$comprow['compcode']]) > 0) {

				$xctotax = $xctot[$comprow['compcode']] * (intval($_REQUEST['ITper'])/100);
				$xctotaxaftr[$comprow['compcode']] = $xctot[$comprow['compcode']] - $xctotax;	

				$mygtooth = $mygtooth + floatval($xctotax);
	?>
		<td align='right'> 
			<b><?=($xctotax<0) ? "(".number_format(abs($xctotax),2).")" : number_format(($xctotax),2)?></b>
		</td>
	<?php
			}
			else{
				echo "<td align='right'> - </td>";
			}
		}
	?>
	<td align='right'> 
		<b><?=($mygtooth<0) ? "(".number_format(abs($mygtooth),2).")" : number_format(($mygtooth),2)?></b>
	</td>
</tr>
<?php
	}
	if($isProvmcit=="True") {
?>

<tr>
	<td colspan='2'>
		<b>PROVISION FOR MCIT (<?=$_REQUEST['MCITper']."%"?> OF GROSS INCOME)</b>
	</td>
	<?php
		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			if(($xctot[$comprow['compcode']]) < 0) {

				$xctotax = $BPROFITzc0[$comprow['compcode']] * (intval($_REQUEST['MCITper'])/100);
				$xctotaxaftr[$comprow['compcode']] = $xctot[$comprow['compcode']] - $xctotax;

				$mygtooth = $mygtooth + floatval($xctotax);
	?>
				<td align='right'> 
					<b><?=($xctotax<0) ? "(".number_format(abs($xctotax),2).")" : number_format(($xctotax),2)?></b>
				</td>
	<?php
			}else{
				echo "<td align='right'> - </td>";
			}
		}
	?>
	<td align='right'> 
		<b><?=($mygtooth<0) ? "(".number_format(abs($mygtooth),2).")" : number_format(($mygtooth),2)?></b>
	</td>
</tr>
<?php
	}
?>
<tr>
	<td colspan='2' style='border-bottom-style: double; border-top: 1px solid #000;'>
		<b>NET INCOME AFTER TAX</b>
	</td>

	<?php
		$mygtooth = 0;
		foreach($arrcomps as $comprow){
			$mygtooth = $mygtooth + floatval($xctotaxaftr[$comprow['compcode']]);
	?>
			<td align='right' style='border-bottom-style: double;; border-top: 1px solid #000;'> 
				<b><?=($xctotaxaftr[$comprow['compcode']]<0) ? "(".number_format(abs($xctotaxaftr[$comprow['compcode']]),2).")" : number_format(($xctotaxaftr[$comprow['compcode']]),2)?></b>
			</td>
	<?php
		}
	?>
	<td align='right' style='border-bottom-style: double; border-top: 1px solid #000;'> 
		<b><?=($mygtooth<0) ? "(".number_format(abs($mygtooth),2).")" : number_format(($mygtooth),2)?></b>
	</td>
</tr>
 
</table>

<form action="TBal_Det.php" name="frmdet" id="frmdet" target="_blank" method="POST">
	<input type="hidden" name="ccode" id="ccode" value="">
	<input type="hidden" name="date1" id="date1" value="">
	<input type="hidden" name="date2" id="date2" value="">
</form>
<?php
	}

?>



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