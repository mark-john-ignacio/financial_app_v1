<?php
	if(!isset($_SESSION)){
		session_start();
	}
	$_SESSION['pageid'] = "InvSumWh";


	ini_set('MAX_EXECUTION_TIME', 900);

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

		$company = $_SESSION['companyid'];
		$sql = "select * From company where compcode='$company'";
		$result=mysqli_query($con,$sql);					
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$compname =  $row['compname'];
		}

		$whse_req = $_REQUEST['selwhfrom'];

		//get FG whse
		$whseFG = "";
		$whseRR = "";
		$whsePRET = "";
		$whseSRET = "";

		$sql = "select * From parameters where compcode='$company' and ccode in ('DEF_WHOUT','DEF_WHIN','DEF_PROUT','DEF_SRIN')";

		$result=mysqli_query($con,$sql);					
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if($row['ccode']=="DEF_WHOUT"){
				$whseFG =  $row['cvalue'];
			}

			if($row['ccode']=="DEF_WHIN"){
				$whseRR =  $row['cvalue']; 
			}

			if($row['ccode']=="DEF_PROUT"){
				$whsePRET =  $row['cvalue'];
			}

			if($row['ccode']=="DEF_SRIN"){
				$whseSRET =  $row['cvalue'];
			}
		}

	//echo $whse_req.": ".$whseFG.", ".$whseRR.", ".$whsePRET.", ".$whseSRET;

	$dtfrom = $_POST["date1"];
	$dtto = $_POST["date2"];

	//BEGINNING BALANCE
	$arravails = array();
	$arritmslist = array();

	$sql = "select a.citemno, b.citemdesc, b.cunit, COALESCE((Sum(a.nqtyin)-sum(a.nqtyout)),0) as nqty
	From tblinventory a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
	where a.compcode='$company' and a.nsection_id='".$whse_req."' and b.linventoriable=0 and a.dcutdate < STR_TO_DATE('$dtfrom', '%m/%d/%Y')
	group by a.citemno, b.citemdesc, b.cunit Order By  b.citemdesc";

	$sqltblinv= mysqli_query($con,$sql);
	$rowTemplate = $sqltblinv->fetch_all(MYSQLI_ASSOC);
	foreach($rowTemplate as $row0){
		$arravails[] = array('citemno' => $row0['citemno'], 'citemdesc' => $row0['citemdesc'], 'cunit' => $row0['cunit'], 'nqty' => $row0['nqty']);
		$arritmslist[] = $row0['citemno'];
	}

	//OTHERS
	$arrothers = array();
	$sql = "select a.citemno, b.citemdesc, b.cunit, a.ctype, a.nsection_id, COALESCE(Sum(a.nqtyin),0) as nqtyin, COALESCE(Sum(a.nqtyout),0) as nqtyout
	From tblinventory a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno 
	where a.compcode='$company' and a.nsection_id='".$whse_req."' and b.linventoriable=0 and a.dcutdate between STR_TO_DATE('$dtfrom', '%m/%d/%Y') and STR_TO_DATE('$dtto', '%m/%d/%Y') group by a.citemno, b.citemdesc, b.cunit, a.ctype, a.nsection_id Order By b.citemdesc";

	$invothers = mysqli_query($con,$sql);
	$rowOthers = $invothers->fetch_all(MYSQLI_ASSOC);
	foreach($rowOthers as $row0){
		$arrothers[] = array('citemno' => $row0['citemno'], 'citemdesc' => $row0['citemdesc'], 'cunit' => $row0['cunit'], 'nqtyin' => $row0['nqtyin'], 'nqtyout' => $row0['nqtyout'], 'ctype' => $row0['ctype']);
		$arritmslist[] = $row0['citemno'];
	}

	//Actual Count / Inventory Ending
	$arrending = array();
	$sql = "select a.citemno, sum(a.nqty) as nqty
	From invcount_t a left join invcount b on a.compcode=b.compcode and a.ctranno=b.ctranno 
	left join items c on a.compcode=c.compcode and a.citemno=c.cpartno 
	where a.compcode='$company' and b.section_nid='".$whse_req."' and b.ctype='ending' and c.linventoriable=0 and b.dcutdate between STR_TO_DATE('$dtfrom', '%m/%d/%Y') and STR_TO_DATE('$dtto', '%m/%d/%Y') group by a.citemno";

	$invending= mysqli_query($con,$sql);
	$rowEnd = $invending->fetch_all(MYSQLI_ASSOC);
	foreach($rowEnd as $row0){
		$arrending[] = array('citemno' => $row0['citemno'],'nqty' => $row0['nqty']);
		$arritmslist[] = $row0['citemno'];
	}


?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../CSS/cssmed.css">
  <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Inventory Summary</title>
</head>

<body style="padding:20px">
<center>
<h2 class="nopadding"><?php echo strtoupper($compname);  ?></h2>
<h3 class="nopadding">Inventory SummaryReport</h3>
<h4 class="nopadding">For the Period <?php echo date_format(date_create($_POST["date1"]),"F d, Y");?> to <?php echo date_format(date_create($_POST["date2"]),"F d, Y");?></h4>
</center>
<br>
<table width="100%" border="0" align="center" cellpadding="3">
  <tr>
		<?php
		$varincnt = 2;
		$varoutcnt = 1;

			if($whse_req==$whseRR){
				$varincnt++;
		?>
    	<td>PU - Purchases</td>
		<?php
			} 
			
			if($whse_req==$whseSRET){
				$varincnt++;
		?>
		<td>SR - Sales Return</td>
		<?php
			} 
		?>
		<td>PROD - Production Outputs</td> 
		<td>IT - Inventory Transfer</td> 
		<?php		
			if($whse_req==$whseFG){
				$varoutcnt++;
		?>
		<td>DR - Delivery Receipt</td>
		<?php
			}

			if($whse_req==$whsePRET){
				$varoutcnt++;
		?>
		<td>PR - Purchase Return</td>
		<?php
			}
		?>
  </tr>

</table>
<br>
<table width="100%" border="0" align="center" cellpadding="3">
  <tr>
    <th colspan="2" rowspan="2" style="text-align:center;">Product</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">UOM</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">Beg</th>
    <th colspan="<?=$varincnt?>" style="text-align:center; border-right:1px solid">Inventory In</th>
	<th rowspan="2" style="text-align:center; border-right:1px solid">Total Available</th>
    <th colspan="<?=$varoutcnt?>" style="text-align:center; border-right:1px solid">Inventory Out</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">Theo End</th>
	<th rowspan="2" style="text-align:center; border-right:1px solid">Actual Count</th>
	<th rowspan="2" style="text-align:center; border-right:1px solid">Variance</th>
    <!--<th rowspan="2" style="text-align:center; border-right:1px solid">Ave Cost/Unit</th>
    <th rowspan="2" style="text-align:center; border-right:1px solid">Ave Retail/Unit</th>-->
  </tr>
  <tr>
		<?php
			if($whse_req==$whseRR){
		?>
    <th style="text-align:center; border-right:1px solid">PU</th>
		<?php
			}
			if($whse_req==$whseSRET){
		?>
    <th style="text-align:center; border-right:1px solid">SR</th>
		<?php
			}
		?>
		<th style="text-align:center; border-right:1px solid">PROD</th>
		<th style="text-align:center; border-right:1px solid">IT</th>
		<?php
			if($whse_req==$whseFG){
		?>
    <th style="text-align:center; border-right:1px solid">DR</th>
		<?php
			}

			if($whse_req==$whsePRET){
		?>
    <th style="text-align:center; border-right:1px solid">PR</th>
		<?php
			}
		?>
    <th style="text-align:center; border-right:1px solid">IT</th>
  </tr>
  <?php

		$rwitms = mysqli_query($con,"Select cpartno as citemno,citemdesc,cunit from items where compcode='$company' and cpartno in ('".implode("','", $arritmslist)."')");
		$rowallietsm = $rwitms->fetch_all(MYSQLI_ASSOC);
		$totIn = 0;
		$totOut = 0; 
		$totVar = 0; 
		foreach($rowallietsm as $rxrow){
	?>
		<tr>
			<td><?php echo $rxrow['citemno'];?></td>
			<td><?php echo strtoupper($rxrow['citemdesc']);?></td>
			<td style="border-right:1px solid"><?php echo $rxrow['cunit'];?></td>
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arravails as $rsx){
						if($rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqty']);
							$totIn = $totIn + floatval($rsx['nqty']);
							break;
						}
					}
				?>
			</td>
			<?php
				if($whse_req==$whseRR){
			?>

			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if($rsx['ctype']=="RR" && $rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqtyin']);
							$totIn = $totIn + floatval($rsx['nqtyin']);
							break;
						}
					}
				?>
			</td>
			<?php
				}

				if($whse_req==$whseSRET){
			?>
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if($rsx['ctype']=="SRet" && $rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqtyin']);
							$totIn = $totIn + floatval($rsx['nqtyin']);
							break;
						}
					}
				?> 
			</td>
			<?php
				}
			?>
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if($rsx['ctype']=="INVCNT" && $rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqtyin']);
							$totIn = $totIn + floatval($rsx['nqtyin']);
							break;
						}
					}
				?>
			</td> 
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if($rsx['ctype']=="INVTRANS" && $rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqtyin']);
							$totIn = $totIn + floatval($rsx['nqtyin']);
							break;
						}
					}
				?>
			</td> 
			<td align="center" style="text-align: center; border-right:1px solid"><b><?=number_format($totIn)?></b></td>

			<?php
				if($whse_req==$whseFG){
			?>
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if(($rsx['ctype']=="DR" || $rsx['ctype']=="DRNT" || $rsx['ctype']=="SI" || $rsx['ctype']=="SINT") && $rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqtyout']);
							$totOut = $totOut + floatval($rsx['nqtyout']);
							break;
						}
					}
				?>
			</td>
			<?php
				}

				if($whse_req==$whsePRET){
			?>
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if($rsx['ctype']=="PRet" && $rsx['citemno']==$rxrow['citemno']){
							echo number_format($rsx['nqtyout']);
							$totOut = $totOut + floatval($rsx['nqtyout']);
							break;
						}
					}
				?>
			</td>
			<?php
				}
			?>
			<td align="center" style="text-align: center; border-right:1px solid">
				<?php
					foreach($arrothers as $rsx){
						if($rsx['ctype']=="INVTRANS" && $rsx['citemno']==$rxrow['citemno']){
							echo (intval($rsx['nqtyout'])!==0) ? number_format($rsx['nqtyout']) : "";
							$totOut = $totOut + floatval($rsx['nqtyout']);
							break;
						}
					}
				?>
			</td> 
			<td align="center" style="text-align: center; border-right:1px solid">
					<b><?=$totIn-$totOut?></b>
			</td>
			<td align="center" style="text-align: center; border-right:1px solid">
				<b>
				<?php
					$actcount = 0;
					foreach($arrending as $rsx){
						if($rsx['citemno']==$rxrow['citemno']){
							$actcount = $rsx['nqty'];
							break;
						}
					}

					echo (intval($actcount)!==0) ? number_format($actcount) : "";
				?>
				</b>
			</td>
			<td align="center" style="text-align: center; border-right:1px solid">
					<b>
						<?php
							$totVar = floatval($actcount) - ($totIn-$totOut);					
							echo (intval($totVar)!==0) ? number_format($totVar) : "";
						?>
					</b>
			</td>
		</tr>
  <?php 
			$totIn = 0;
			$totOut = 0;
			$totVar = 0;
		}
	?>
</table>

</body>
</html>

