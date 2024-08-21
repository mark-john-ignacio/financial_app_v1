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
		$sql = "Select A.compcode, MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
				From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
				where ".$qrydte." and IFNULL(B.cacctdesc,'') <> ''
				and B.cFinGroup = 'Income Statement'
				Group By A.compcode, MONTH(ddate), A.acctno, B.cacctdesc
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

	function gettotal($acctid, $xctype, $xmo, $ccode){
		global $darray;

		$xtot = 0;
		foreach($darray as $rsp){
			if($rsp['acctno']==$acctid && $rsp['dmonth']==$xmo && $rsp['compcode']==$ccode){

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


<br><br>
<table width="100%" border="0" align="center" cellpadding="3" class="my-table">
  <tr>

    <th style="text-align:center" width="100px" rowspan="2">Account No. </th>
    <th style="text-align:center" rowspan="2">Account Name</th>
    <?php
		foreach($hdr_months as $rx){
			$monthNum  = $rx;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('F');
	?>
    	<th style="text-align:center; border-right: 1px solid #3d3e3e" colspan="3"><?=$monthName?></th>
	<?php
		}
	?>
	<th style="text-align:center" colspan="3">GRAND TOTAL</th>
  </tr>
  <tr>
  	<?php
	foreach($hdr_months as $rx){
		foreach($arrcomps as $row){
	?>
		<th style="text-align:center; border-right: 1px solid #3d3e3e"><?=$row['compname']?></th>
	<?php
		}
		echo "<th style=\"text-align:center\">TOTAL</th>";
	}

		foreach($arrcomps as $row){
			echo "<th style=\"text-align:center\">".$row['compname']."</th>";
		}
		echo "<th style=\"text-align:center\">TOTAL</th>";
	?>
  </tr>
 <?php

 //echo "<pre>";
 //print_r($mainarray);
 //echo "</pre>";

	$arrlvl = array();
	$arrlvldsc = array();

	$GrTots = array();
	foreach($arrcomps as $cmprw){
		$GrTots[$cmprw['compcode']] = 0;
	}


	foreach($hdr_months as $rxzm){
		foreach($arrcomps as $cmprw){
			$profitRevn[$cmprw['compcode'].$rxzm] = 0;
			$profitCost[$cmprw['compcode'].$rxzm] = 0;
			$BPROFITzc0[$cmprw['compcode'].$rxzm] = 0;
			$BPEXPzc0[$cmprw['compcode'].$rxzm] = 0;

			$arrlvlamt[$cmprw['compcode']."0"][$rxzm] = 0;
			$arrlvlamt[$cmprw['compcode']."1"][$rxzm] = 0;
			$arrlvlamt[$cmprw['compcode']."2"][$rxzm] = 0;
			$arrlvlamt[$cmprw['compcode']."3"][$rxzm] = 0;
			$arrlvlamt[$cmprw['compcode']."4"][$rxzm] = 0;
			$arrlvlamt[$cmprw['compcode']."5"][$rxzm] = 0;
		}
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
						$mygtooth = 0;
						foreach($arrcomps as $cmprw){

							$toformat = (floatval($arrlvlamt[$cmprw['compcode'].intval($x)][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[$cmprw['compcode'].intval($x)][$rxzm]),2).")": number_format($arrlvlamt[$cmprw['compcode'].intval($x)][$rxzm],2);

							$mygtooth = $mygtooth + floatval($arrlvlamt[$cmprw['compcode'].intval($x)][$rxzm]);

							echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid; padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";
						}

						echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid; border-right: 1px solid #3d3e3e'><b> ".number_format($mygtooth,2)." </b</td>";

						$arrlvlamt[$cmprw['compcode'].intval($x)][$rxzm] = 0;

					}

					echo "</tr>";
				}

			}

			$GENxyz1 = getpads($row['nlevel']);

			echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($row['nlevel'])]."</b></td>";

			foreach($hdr_months as $rxzm){

				$mygtooth = 0;
				foreach($arrcomps as $cmprw){

					$toformat = (floatval($arrlvlamt[$cmprw['compcode'].intval($row['nlevel'])][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[$cmprw['compcode'].intval($row['nlevel'])][$rxzm]),2).")": number_format($arrlvlamt[$cmprw['compcode'].intval($row['nlevel'])][$rxzm],2);

					$mygtooth = $mygtooth + floatval($arrlvlamt[$cmprw['compcode'].intval($row['nlevel'])][$rxzm]);
					$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($arrlvlamt[$cmprw['compcode'].intval($row['nlevel'])][$rxzm]);

					echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";

					

				}
				echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid; border-right: 1px solid #3d3e3e'><b> ".number_format($mygtooth,2)." </b</td>";

				$arrlvlamt[$cmprw['compcode'].intval($row['nlevel'])][$rxzm] = 0;

			}

			//For GTotal
			$xcpus = 0; //$GrTots[$cmprw['compcode']]
			foreach($arrcomps as $cmprw){
				$abc = 0;
				if(floatval($GrTots[$cmprw['compcode']]) > 0){
					$abc = number_format($GrTots[$cmprw['compcode']],2);
				}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
					$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
				}else{
					$abc = "-";
				}

				$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

				echo "<td style=\"border-bottom: 1px solid; border-top: 1px solid; text-align:right; font-weight: bold\"> ".$abc." </td>";

				$GrTots[$cmprw['compcode']] = 0;
			}

			echo "<td style=\"border-bottom: 1px solid; border-top: 1px solid; text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

			echo "</tr>";
			$xcpus = 0;
		}

		if($row['ctype']=="General"){

			$arrlvl[$row['nlevel']] = $row['cacctid']; 
			$arrlvldsc[$row['nlevel']] = $row['cacctdesc']; 

		}
		
		if($ccate!==$row['ccategory']){
	

			echo "<tr><td colspan='2' style='border-bottom-style: double; border-top: 2px solid #000;'><b>TOTAL ".$ccate."</b></td>";

			foreach($hdr_months as $rxzm){

				$mygtooth = 0;
				foreach($arrcomps as $cmprw){

					$toformat = (floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[$cmprw['compcode']."0"][$rxzm]),2).")": number_format($arrlvlamt[$cmprw['compcode']."0"][$rxzm],2);

					$mygtooth = $mygtooth + floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);
					$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);

					echo "<td align='right' style='border-bottom-style: double; border-top: 2px solid #000; padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";

					if($ccate=="REVENUE"){
						$profitRevn[$cmprw['compcode'].$rxzm] = floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);
					}
		
					if($ccate=="COST OF SALES"){				
						$profitCost[$cmprw['compcode'].$rxzm] = floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);
					}

					$arrlvlamt[$cmprw['compcode']."0"][$rxzm] = 0;
				}
				echo "<td align='right' style='border-bottom-style: double; border-top: 2px solid #000; border-right: 1px solid #3d3e3e'><b> ".number_format($mygtooth,2)." </b</td>";
			
			}

			//For GTotal
			$xcpus = 0; //$GrTots[$cmprw['compcode']]
			foreach($arrcomps as $cmprw){
				$abc = 0;
				if(floatval($GrTots[$cmprw['compcode']]) > 0){
					$abc = number_format($GrTots[$cmprw['compcode']],2);
				}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
					$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
				}else{
					$abc = "-";
				}

				$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

				echo "<td style=\"border-bottom-style: double; border-top: 2px solid #000;text-align:right; font-weight: bold\"> ".$abc." </td>";

				$GrTots[$cmprw['compcode']] = 0;
			}

			echo "<td style=\"border-bottom-style: double; border-top: 2px solid #000;text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

			echo "</tr>";
			$xcpus = 0;


			if($row['ccategory']=="EXPENSES"){

					echo "<tr><td colspan='2' style='padding-top:10px'><b>GROSS PROFIT</b></td>";
					
					foreach($hdr_months as $rxzm){

						$mygtooth = 0;
						foreach($arrcomps as $cmprw){

							$BPROFITzc0[$cmprw['compcode'].$rxzm] = floatval($profitRevn[$cmprw['compcode'].$rxzm]) - floatval($profitCost[$cmprw['compcode'].$rxzm]);
							$donetwo = ($BPROFITzc0[$cmprw['compcode'].$rxzm]<0) ? "(".number_format(abs($BPROFITzc0[$cmprw['compcode'].$rxzm]),2).")" : number_format(($BPROFITzc0[$cmprw['compcode'].$rxzm]),2);

							$mygtooth = $mygtooth + floatval($BPROFITzc0[$cmprw['compcode'].$rxzm]);
							$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($BPROFITzc0[$cmprw['compcode'].$rxzm]);

							echo "<td align='right' style='border-bottom: 1px solid #000; padding-top: 10px; padding-right: 20px; padding-left: 20px;'><b>".$donetwo."</b></td>";

						}

						echo "<td align='right' style='border-bottom: 1px solid #000; padding-top: 10px; border-top: 1px solid; border-right: 1px solid #3d3e3e'><b> ".number_format($mygtooth,2)." </b</td>";
					}

					//For GTotal
					$xcpus = 0; //$GrTots[$cmprw['compcode']]
					foreach($arrcomps as $cmprw){
						$abc = 0;
						if(floatval($GrTots[$cmprw['compcode']]) > 0){
							$abc = number_format($GrTots[$cmprw['compcode']],2);
						}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
							$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
						}else{
							$abc = "-";
						}

						$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

						echo "<td style=\"border-bottom: 1px solid; border-top: 1px solid; text-align:right; font-weight: bold; padding-top: 10px;\"> ".$abc." </td>";

						$GrTots[$cmprw['compcode']] = 0;
					}

					echo "<td style=\"border-bottom: 1px solid; border-top: 1px solid; text-align:right; font-weight: bold; padding-top: 10px;\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

					echo "</tr>";
					$xcpus = 0;

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
				$mygtooth = 0;

				foreach($arrcomps as $cmprw){
		?>
				<td style="text-align:right; padding-right: 20px; padding-left: 20px ">
					<?php
						$xcvb = 0;
						if($row['ctype']=="Details"){

							$xcvb = gettotal($row['cacctid'], $row['ccategory'], $rxzm, $cmprw['compcode']);

							for ($x = 0; $x < intval($row['nlevel']); $x++) {
								$arrlvlamt[$cmprw['compcode'].$x][$rxzm] = $arrlvlamt[$cmprw['compcode'].$x][$rxzm] + $xcvb;
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
					$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($xcvb);
				}
		?>
			<td style="text-align:right; border-right: 1px solid #3d3e3e; font-weight: bold">
				<?php
					if($row['ctype']=="Details"){
						if(floatval($mygtooth) > 0){
							echo number_format($mygtooth,2);
						}else if(floatval($mygtooth) < 0){
							echo "(".abs(number_format($mygtooth,2)).")";
						}else{
							echo "-";
						}
					}
				?>
			</td>
		<?php
			}

			$xcpus = 0;
			foreach($arrcomps as $cmprw){
		?>
				<td style="text-align:right; font-weight: bold"> 
				
					<?php
						if($row['ctype']=="Details"){
							if(floatval($GrTots[$cmprw['compcode']]) > 0){
								echo number_format($GrTots[$cmprw['compcode']],2);
							}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
								echo "(".abs(number_format($GrTots[$cmprw['compcode']],2)).")";
							}else{
								echo "-";
							}

							$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);
						}
					?> 
					
				</td>
		<?php
				$GrTots[$cmprw['compcode']] = 0;
			}
		?>
		<td style="text-align:right; font-weight: bold">
			<?php
				if($row['ctype']=="Details"){
					if(floatval($xcpus) > 0){
						echo number_format($xcpus,2);
					}else if(floatval($xcpus) < 0){
						echo "(".abs(number_format($xcpus,2)).")";
					}else{
						echo "-";
					}

					$xcpus = 0;
				}
			?>
		</td>
  	</tr>
<?php

		$ccate = $row['ccategory'];
		$arrlvlcnt = $row['nlevel'];

	}

	if(intval($arrlvlcnt) !== 1){

		$GENxyz1 = getpads($arrlvlcnt-1);

		echo "<tr><td>&nbsp;</td><td style='padding-left: ".$GENxyz1 ."px'><b>Total ".$arrlvldsc[intval($arrlvlcnt)-1]."</b></td>";

		foreach($hdr_months as $rxzm){

			$mygtooth = 0;
			foreach($arrcomps as $cmprw){

				$toformat = (floatval($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm]),2).")": number_format($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm],2);

				$mygtooth = $mygtooth + floatval($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm]);

				echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid; padding-right: 20px; padding-left: 20px;'><b>".$toformat."</b></td>";

			}

			echo "<td align='right' style='border-bottom: 1px solid; border-top: 1px solid;'><b> ".number_format($mygtooth,2)." </b</td>";
		}

		echo "</tr>";

	}

		echo "<tr><td colspan='2' style='border-bottom: 2px solid #000; border-top: 1px solid #000;'><b>TOTAL ".$ccate."</b></td>";

		foreach($hdr_months as $rxzm){

			$mygtooth = 0;
			foreach($arrcomps as $cmprw){

				$donetwo = ($arrlvlamt[$cmprw['compcode']."0"][$rxzm]<0) ? "(".number_format(abs($arrlvlamt[$cmprw['compcode']."0"][$rxzm]),2).")" : number_format(($arrlvlamt[$cmprw['compcode']."0"][$rxzm]),2);

				$toformat = (floatval($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm])<0) ? "(".number_format(abs($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm]),2).")": number_format($arrlvlamt[$cmprw['compcode'].intval($arrlvlcnt)-1][$rxzm],2);

				$mygtooth = $mygtooth + floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);
				$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);

				echo "<td align='right' style='border-bottom: 2px solid #000; border-top: 1px solid #000; ; padding-right: 20px; padding-left: 20px;'><b>".$donetwo."</b></td>";

				if($ccate=="EXPENSES"){
					$BPEXPzc0[$cmprw['compcode'].$rxzm] = floatval($arrlvlamt[$cmprw['compcode']."0"][$rxzm]);
				}
			
				$xctot[$cmprw['compcode'].$rxzm] = $BPROFITzc0[$cmprw['compcode'].$rxzm]-$BPEXPzc0[$cmprw['compcode'].$rxzm];
				$xctotax[$cmprw['compcode'].$rxzm] = 0;
				$xctotaxaftr[$cmprw['compcode'].$rxzm] = 0;

			}

			echo "<td align='right' style='border-bottom: 2px solid; border-top: 1px solid; border-right: 1px solid #3d3e3e;'><b> ".number_format($mygtooth,2)." </b</td>";
		}

		//For GTotal
		$xcpus = 0; //$GrTots[$cmprw['compcode']]
		foreach($arrcomps as $cmprw){
			$abc = 0;
			if(floatval($GrTots[$cmprw['compcode']]) > 0){
				$abc = number_format($GrTots[$cmprw['compcode']],2);
			}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
				$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
			}else{
				$abc = "-";
			}

			$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

			echo "<td style=\"border-bottom: 2px solid; border-top: 1px solid; text-align:right; font-weight: bold\"> ".$abc." </td>";

			$GrTots[$cmprw['compcode']] = 0;
		}

		echo "<td style=\"border-bottom: 2px solid; border-top: 1px solid; text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

		echo "</tr>";
		$xcpus = 0;
		

?>

<tr><td colspan='3' style='padding-top: 10px'>&nbsp;</td></tr>
<tr>
	<td colspan='2' style='border-bottom-style: double; border-top: 1px solid #000;'>
		<b>NET INCOME/(LOSS) BEFORE TAX</b>
	</td>
	<?php
		foreach($hdr_months as $rxzm){
			$mygtooth = 0;
			foreach($arrcomps as $cmprw){

				$mygtooth = $mygtooth + floatval($xctot[$cmprw['compcode'].$rxzm]);
				$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($xctot[$cmprw['compcode'].$rxzm]);
	?>
		<td align='right' style='border-bottom-style: double; border-top: 1px solid #000; padding-right: 20px; padding-left: 20px; '> 
			<b><?=($xctot[$cmprw['compcode'].$rxzm]<0) ? "(".number_format(abs($xctot[$cmprw['compcode'].$rxzm]),2).")" : number_format(($xctot[$cmprw['compcode'].$rxzm]),2)?></b>
		</td>
	<?php
			}

			echo "<td align='right' style='border-bottom-style: double; border-top: 1px solid #000;'><b> ".number_format($mygtooth,2)." </b</td>";

		}

		//For GTotal
		$xcpus = 0; //$GrTots[$cmprw['compcode']]
		foreach($arrcomps as $cmprw){
			$abc = 0;
			if(floatval($GrTots[$cmprw['compcode']]) > 0){
				$abc = number_format($GrTots[$cmprw['compcode']],2);
			}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
				$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
			}else{
				$abc = "-";
			}

			$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

			echo "<td style=\"border-bottom-style: double; border-top: 1px solid #000; text-align:right; font-weight: bold\"> ".$abc." </td>";

			$GrTots[$cmprw['compcode']] = 0;
		}

		echo "<td style=\"border-bottom-style: double; border-top: 1px solid #000; text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

		$xcpus = 0;
	?>
	
</tr>

<?php
	$isPrvo  = "False";
	$isProvmcit = "False";

	foreach($hdr_months as $rxzm){
		foreach($arrcomps as $comprow){
			if(($xctot[$comprow['compcode'].$rxzm]) > 0) {	
				$isPrvo  = "True";
			}

			if(($xctot[$comprow['compcode'].$rxzm]) < 0) {
				$isProvmcit = "True";
			}
		}
	}

	if($isPrvo=="True") {			
?>

<tr>
	<td colspan='2'>
		<b>PROVISION FOR INCOME TAX <?=$_REQUEST['ITper']."%"?></b> 
	</td>
	<?php
		foreach($hdr_months as $rxzm){

			$mygtooth = 0;
			foreach($arrcomps as $cmprw){

				if(($xctot[$cmprw['compcode'].$rxzm]) > 0) {
					$xctotax[$cmprw['compcode'].$rxzm] = $xctot[$cmprw['compcode'].$rxzm] * (intval($_REQUEST['ITper'])/100);
					$xctotaxaftr[$cmprw['compcode'].$rxzm] = $xctot[$cmprw['compcode'].$rxzm] - $xctotax[$cmprw['compcode'].$rxzm];

					$mygtooth = $mygtooth + floatval($xctotax[$cmprw['compcode'].$rxzm]);
					$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($xctotax[$cmprw['compcode'].$rxzm]);
	?>
					<td align='right' style="padding-right: 20px; padding-left: 20px;"> 
						<b><?=($xctotax[$cmprw['compcode'].$rxzm]<0) ? "(".number_format(abs($xctotax[$cmprw['compcode'].$rxzm]),2).")" : number_format(($xctotax[$cmprw['compcode'].$rxzm]),2)?></b>
					</td>
	<?php
				}else{
					echo "<td align='right'  style=\"padding-right: 20px; padding-left: 20px;\">-</td>";
				}
			}

			echo "<td align='right'><b> ".number_format($mygtooth,2)." </b</td>";

		}

		//For GTotal
		$xcpus = 0; //$GrTots[$cmprw['compcode']]
		foreach($arrcomps as $cmprw){
			$abc = 0;
			if(floatval($GrTots[$cmprw['compcode']]) > 0){
				$abc = number_format($GrTots[$cmprw['compcode']],2);
			}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
				$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
			}else{
				$abc = "-";
			}

			$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

			echo "<td style=\"text-align:right; font-weight: bold\"> ".$abc." </td>";

			$GrTots[$cmprw['compcode']] = 0;
		}

		echo "<td style=\"text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

		$xcpus = 0;
	?>
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
		foreach($hdr_months as $rxzm){

			$mygtooth = 0;
			foreach($arrcomps as $cmprw){

				if(($xctot[$cmprw['compcode'].$rxzm]) < 0) {
					$xctotax[$cmprw['compcode'].$rxzm] = $BPROFITzc0[$cmprw['compcode'].$rxzm] * (intval($_REQUEST['MCITper'])/100);
					$xctotaxaftr[$cmprw['compcode'].$rxzm] = $xctot[$cmprw['compcode'].$rxzm] - $xctotax[$cmprw['compcode'].$rxzm];

					$mygtooth = $mygtooth + floatval($xctotax[$cmprw['compcode'].$rxzm]);
					$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($xctotax[$cmprw['compcode'].$rxzm]);
	?>
					<td align='right' style="padding-right: 20px; padding-left: 20px;"> 
						<b><?=($xctotax[$cmprw['compcode'].$rxzm]<0) ? "(".number_format(abs($xctotax[$cmprw['compcode'].$rxzm]),2).")" : number_format(($xctotax[$cmprw['compcode'].$rxzm]),2)?></b>
					</td>
	<?php
				}else{
					echo "<td align='right' style=\"padding-right: 20px; padding-left: 20px;\">-</td>";
				}
			}
			
			echo "<td align='right'><b> ".((floatval($mygtooth) > 0) ? number_format($mygtooth,2) : ("(".number_format(abs($mygtooth),2)) .")")." </b</td>";
		}

		//For GTotal
		$xcpus = 0; //$GrTots[$cmprw['compcode']]
		foreach($arrcomps as $cmprw){
			$abc = 0;

			if(floatval($GrTots[$cmprw['compcode']]) > 0){
				$abc = number_format($GrTots[$cmprw['compcode']],2);
			}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
				$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
			}else{
				$abc = "-";
			}

			$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

			echo "<td style=\"text-align:right; font-weight: bold\"> ".$abc." </td>";

			$GrTots[$cmprw['compcode']] = 0;
		}

		echo "<td style=\"text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

		$xcpus = 0;
	?>
</tr>
<?php
	}
?>
<tr>
	<td colspan='2' style='border-bottom-style: double;'>
		<b>NET INCOME AFTER TAX</b>
	</td>
	<?php
		foreach($hdr_months as $rxzm){
			$mygtooth = 0;
			foreach($arrcomps as $cmprw){
				$mygtooth = $mygtooth + floatval($xctotaxaftr[$cmprw['compcode'].$rxzm]);
				$GrTots[$cmprw['compcode']] = $GrTots[$cmprw['compcode']] + floatval($xctotaxaftr[$cmprw['compcode'].$rxzm]);
	?>
				<td align='right' style='border-bottom-style: double; padding-right: 20px; padding-left: 20px;"'> 
					<b><?=($xctotaxaftr[$cmprw['compcode'].$rxzm]<0) ? "(".number_format(abs($xctotaxaftr[$cmprw['compcode'].$rxzm]),2).")" : number_format(($xctotaxaftr[$cmprw['compcode'].$rxzm]),2)?></b>
				</td>
	<?php
			}

			echo "<td align='right' style='border-bottom-style: double'><b> ".number_format($mygtooth,2)." </b</td>";
		}

		//For GTotal
		$xcpus = 0; //$GrTots[$cmprw['compcode']]
		foreach($arrcomps as $cmprw){
			$abc = 0;

			if(floatval($GrTots[$cmprw['compcode']]) > 0){
				$abc = number_format($GrTots[$cmprw['compcode']],2);
			}else if(floatval($GrTots[$cmprw['compcode']]) < 0){
				$abc = "(".number_format(abs($GrTots[$cmprw['compcode']]),2).")";
			}else{
				$abc = "-";
			}

			$xcpus = $xcpus + floatval($GrTots[$cmprw['compcode']]);

			echo "<td style=\"border-bottom-style: double; text-align:right; font-weight: bold\"> ".$abc." </td>";

			$GrTots[$cmprw['compcode']] = 0;
		}

		echo "<td style=\"border-bottom-style: double; text-align:right; font-weight: bold\"> ".((floatval($xcpus) > 0) ? number_format($xcpus,2) : number_format(abs($xcpus),2))." </td>";

		$xcpus = 0;
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