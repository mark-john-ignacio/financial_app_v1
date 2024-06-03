<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	$tranno = $_POST['hdntransid'];


	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
		}

	}
	
	$arrmrpjo = array();
	$sql = "select X.*, A.citemdesc, C.cname, D.cdesc as secdesc from mrp_jo X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno left join customers C on X.compcode=C.compcode and X.ccode=C.cempid left join locations D on X.compcode=D.compcode and X.location_id=D.nid where X.compcode='$company' and X.ctranno = '$tranno'";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo[] = $row2;				
	}

	$arrmrpjoproc = array();
	$sql = "select X.*, A.citemdesc from mrp_jo_process X left join items A on X.compcode=A.compcode and X.citemno=A.cpartno where X.compcode='$company' and X.mrp_jo_ctranno = '$tranno' Order By X.nid";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjoproc[] = $row2;
	}

	$arrmrpjo_pt = array();
	$sql = "select X.nid, X.ctranno, X.mrp_process_id, X.mrp_process_desc, X.nmachineid, IFNULL(X.ddatestart,'') as ddatestart, IFNULL(ddateend,'') as ddateend, X.nactualoutput, X.operator_id, X.nrejectqty, X.nscrapqty, IFNULL(Y.cdesc,'') as cmachinedesc, IFNULL(Z.cdesc,'') as operator_name, IFNULL(ZY.cdesc,'') as qc_name, X.lpause, IFNULL(X.cremarks,'') as cremarks from mrp_jo_process_t X left join mrp_machines Y on X.compcode=Y.compcode and X.nmachineid=Y.nid left join mrp_operators Z on X.compcode=Z.compcode and X.operator_id=Z.nid left join mrp_operators ZY on X.compcode=ZY.compcode and X.cqcpostedby=ZY.nid where X.compcode='$company' and (X.ctranno in (Select ctranno from mrp_jo_process where compcode='$company' and mrp_jo_ctranno  = '$tranno') OR ctranno  = '$tranno')";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo_pt[] = $row2;				
	}

	$arrmrpjo_ptmain = array();
	$sql = "select X.nid, X.ctranno, X.mrp_process_id, X.mrp_process_desc, X.nmachineid, IFNULL(X.ddatestart,'') as ddatestart, IFNULL(ddateend,'') as ddateend, X.nactualoutput, X.operator_id, X.nrejectqty, X.nscrapqty, IFNULL(Y.cdesc,'') as cmachinedesc, IFNULL(Z.cdesc,'') as operator_name, IFNULL(ZY.cdesc,'') as qc_name, X.lpause, IFNULL(X.cremarks,'') as cremarks from mrp_jo_process_t X left join mrp_machines Y on X.compcode=Y.compcode and X.nmachineid=Y.nid left join mrp_operators Z on X.compcode=Z.compcode and X.operator_id=Z.nid left join mrp_operators ZY on X.compcode=ZY.compcode and X.cqcpostedby=ZY.nid where X.compcode='$company' and X.ctranno  = '$tranno'";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo_ptmain[] = $row2;				
	}

?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Verdana, sans-serif;
			font-size: 9pt;
		}
		.tdpadx{
			padding-top: 5px; 
			padding-bottom: 5px
		}
		.tddetz{
			border-left: 1px solid; 
			border-right: 1px solid;
		}
		.tdright{
			padding-right: 10px;
		}
		#imgcontent {
        	position: relative;
		}
		#imgcontent img {
			position: absolute;
			top: 2px;
			left: 3px;
		}
	</style>
</head>

<body onLoad="window.print()"> <!-- onLoad="window.print()" -->

<table border="0" width="100%"  id="tblMain">
	<tr>
		<td align="left" width="250px"> 
			<img src="<?php echo "../".$logosrc; ?>" width="150px">
		</td>
		<!--<td align="left"> 

				<table border="0" width="100%">
						<tr>
							<td><font style="font-size: 18px;"><?//php echo $logonamz; ?></font></td>
						</tr>
						<tr>
							<td><font><?//php echo $logoaddrs; ?></font></td>
						</tr>
				</table>

		</td>-->
		<td align="center"> 
			<h1>JOB ORDER</h1>
		</td>
	</tr>
	
</table>

<!-- MAIN JO PRINT -->
<table border="0" width="100%" cellpadding="1px"  id="tblMain">
	<tr>
		<td width="100px"><b>Date Release: </b></td>
		<td> <?=date_format(date_create($arrmrpjo[0]['ddate']), "M d, Y")?> </td>
		<td width="100px"><b>SO No.: </b></td>
		<td> <?=$arrmrpjo[0]['crefSO']?> </td>
	</tr>

	<tr>
		<td width="100px"><b>Customer: </b></td>
		<td> <?=$arrmrpjo[0]['cname']?> </td>
		<td width="100px"><b>JO No.: </b></td>
		<td> <?=$tranno?> </td>
	</tr>

	<tr>
		<td colspan="4"> 
			<table border="0" width="100%">
				<td> <b>Department: </b> <?=($arrmrpjo[0]['secdesc'])?></td> 
				<td> <b>Priority: </b> <?=($arrmrpjo[0]['cpriority'])?></td>
				<td> <b>Target Date (Finished): </b> <?=date_format(date_create($arrmrpjo[0]['dtargetdate']),"M d, Y")?></td>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="4" style="padding-top: 10px"> 
			<table border="0" width="100%">
				<tr>
					<th> Item </th> 
					<th> JO Qty</th>
					<th> Working Hrs</th>
					<th> Setup Time</th>
					<th> Cycle Time</th>
					<th> Total Time</th>
				</tr>

				<tr>
					<td align="center"> <?=$arrmrpjo[0]['citemdesc']?> </td>
					<td align="center"> <?=number_format($arrmrpjo[0]['nqty'])?> </td>
					<td align="center"> <?=number_format($arrmrpjo[0]['nworkhrs'],2)?> </td>
					<td align="center"> <?=number_format($arrmrpjo[0]['nsetuptime'],2)?> </td>
					<td align="center"> <?=number_format($arrmrpjo[0]['ncycletime'],2)?> </td>
					<td align="center"> <?=number_format($arrmrpjo[0]['ntottime'],2)?> </td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="4" style="padding-top: 10px; border-top: 1px solid"> 
			<table border="0" width="100%">
				<tr>
					<th width="25%"> Customer PO / Ref Work Week: </th> 
					<td width="25%"> <?=$arrmrpjo[0]['cnarration']?></td>
					<th width="25%"> Product Type</th>
					<td width="25%"> <?=$arrmrpjo[0]['cproductype']?></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="4"> 
			<table border="1" width="100%" style="border-collapse: collapse;">
				<tr>
					<td width="33%" valign="top" height="50px"> <b> Prepared by: </b></td> 
					<td width="34%" valign="top" height="50px"> <b> Checked by: </b></td> 
					<td width="33%" valign="top" height="50px"> <b> Approved by: </b></td> 
				</tr>
			</table>
		</td>
	</tr>
</table>

<br><br>

<table border="1" width="100%" cellpadding="3px"  id="tblMain" style="border-collapse: collapse;">
	<tr>
		<th> Machine </th> 
		<th> Process</th>
		<th> Date Started</th>
		<th> Date Ended</th>
		<th> Actual Output</th>
		<th> Operator</th>
		<th> Reject Qty</th>
		<th> Scrap Qty</th>
		<th> QC</th>
		<th> Remarks</th>
	</tr>

	<?php
		$totrej = 0;
		$totscrp = 0;
		foreach($arrmrpjoproc as $rsc){
	?>
		<tr> 
				<td>&nbsp;</td>
				<td> <b><?=$rsc['citemdesc']?><b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>				
		</tr>

		<?php
			foreach($arrmrpjo_pt as $bv){
				if($rsc['ctranno']==$bv['ctranno']){
		?>

			<tr> 
				<td><?=$bv['cmachinedesc']?> </td> 
				<td style="padding-left:10px"><?=$bv['mrp_process_desc']?> </td>
				<td><?=$bv['ddatestart']?></td>
				<td><?=$bv['ddateend']?></td>
				<td style="text-align: center"><?=number_format($bv['nactualoutput'])?></td>
				<td><?=$bv['operator_name']?></td>
				<td style="text-align: center"><?=number_format($bv['nrejectqty'])?></td>
				<td style="text-align: center"><?=number_format($bv['nscrapqty'])?></td>
				<td><?=$bv['qc_name']?></td>
				<td><?=$bv['cremarks']?></td>				
			</tr>
		<?php
					$totrej = $totrej + floatval($bv['nrejectqty']);
					$totscrp = $totscrp + floatval($bv['nscrapqty']);
				}
			}
		?>
	<?php
		}

		if(count($arrmrpjoproc)>=1){
	?>

			<tr> 
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>				
			</tr>

		<?php
		}
		
			foreach($arrmrpjo_ptmain as $bv){
		?>

			<tr> 
				<td><?=$bv['cmachinedesc']?> </td> 
				<td style="padding-left:10px"><?=$bv['mrp_process_desc']?> </td>
				<td><?=$bv['ddatestart']?></td>
				<td><?=$bv['ddateend']?></td>
				<td style="text-align: center"><?=number_format($bv['nactualoutput'])?></td>
				<td><?=$bv['operator_name']?></td>
				<td style="text-align: center"><?=number_format($bv['nrejectqty'])?></td>
				<td style="text-align: center"><?=number_format($bv['nscrapqty'])?></td>
				<td><?=$bv['qc_name']?></td>
				<td><?=$bv['cremarks']?></td>				
			</tr>
		<?php
				$totrej = $totrej + floatval($bv['nrejectqty']);
				$totscrp = $totscrp + floatval($bv['nscrapqty']);
			}
		?>

			<tr> 
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><b> Total: </b></td>
				<td style="text-align: center"><?=number_format($totrej)?></td>
				<td style="text-align: center"><?=number_format($totscrp)?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>				
			</tr>
</table>
<br>
<table border="1" width="100%" style="border-collapse: collapse;">
	<tr>
		<td width="30%" valign="top" height="50px"> <b> REMARKS: </b></td> 
		<td valign="top" height="50px"> 
			<table border="0" width="100%">
				<tr>
					<th colspan="4"> RECEIVED BY </th> 
				</tr>
				<tr>
					<th width="20%" style="text-align: left"> Production: </th>
					<td>&nbsp;</td> 
					<th width="20%" style="text-align: left"> Warehouse: </th>
					<td>&nbsp;</td> 
				</tr>
				<tr>
					<th width="20%" style="text-align: left"> Date: </th>
					<td>&nbsp;</td> 
					<th width="20%" style="text-align: left"> Date: </th>
					<td>&nbsp;</td> 
				</tr>
			</table>
		</td> 
	</tr>
</table>
<!-- END MAIN JO PRINT --> 


<!-- SUB JO PRINT -->
<?php
	foreach($arrmrpjoproc as $rsc){
?>

	<table border="0" width="100%"  id="tblMain" style="page-break-before:always">
		<tr>
			<td align="left" width="250px"> 
				<img src="<?php echo "../".$logosrc; ?>" width="150px">
			</td>
			<!--<td align="left"> 

					<table border="0" width="100%">
							<tr>
								<td><font style="font-size: 18px;"><?//php echo $logonamz; ?></font></td>
							</tr>
							<tr>
								<td><font><?//php echo $logoaddrs; ?></font></td>
							</tr>
					</table>

			</td>-->
			<td align="center"> 
				<h1>JOB ORDER</h1>
			</td>
		</tr>
		
	</table>

	<table border="0" width="100%" cellpadding="1px"  id="tblMain" >
		<tr>
			<td width="100px"><b>Date Release: </b></td>
			<td> <?=date_format(date_create($arrmrpjo[0]['ddate']), "M d, Y")?> </td>
			<td width="100px"><b>Main JO No.: </b></td>
			<td> <?=$tranno?> </td>
		</tr>

		<tr>
			<td width="100px"><b>Customer: </b></td>
			<td> <?=$arrmrpjo[0]['cname']?> </td>
			<td width="100px"><b>Sub JO No.: </b></td>
			<td> <?=$rsc['ctranno']?> </td>
		</tr>

		<tr>
			<td colspan="4"> 
				<table border="0" width="100%">
					<td> <b>Department: </b> <?=($arrmrpjo[0]['secdesc'])?></td> 
					<td> <b>Priority: </b> <?=($arrmrpjo[0]['cpriority'])?></td>
					<td> <b>Target Date (Finished): </b> <?=date_format(date_create($arrmrpjo[0]['dtargetdate']),"d-m-Y")?></td>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="4" style="padding-top: 10px"> 
				<table border="0" width="100%">
					<tr>
						<th> Item </th> 
						<th> JO Qty</th>
						<th> Working Hrs</th>
						<th> Setup Time</th>
						<th> Cycle Time</th>
						<th> Total Time</th>
					</tr>

					<tr>
						<td align="center"> <?=$rsc['citemdesc']?> </td>
						<td align="center"> <?=number_format($rsc['nqty'])?> </td>
						<td align="center"> <?=number_format($rsc['nworkhrs'],2)?> </td>
						<td align="center"> <?=number_format($rsc['nsetuptime'],2)?> </td>
						<td align="center"> <?=number_format($rsc['ncycletime'],2)?> </td>
						<td align="center"> <?=number_format($rsc['ntottime'],2)?> </td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="4" style="padding-top: 10px; border-top: 1px solid"> 
				<table border="0" width="100%">
					<tr>
						<th width="25%"> Customer PO / Ref Work Week: </th> 
						<td width="25%"> <?=$arrmrpjo[0]['cnarration']?></td>
						<th width="25%"> Product Type</th>
						<td width="25%"> <?=$arrmrpjo[0]['cproductype']?></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="4"> 
				<table border="1" width="100%" style="border-collapse: collapse;">
					<tr>
						<td width="33%" valign="top" height="50px"> <b> Prepared by: </b></td> 
						<td width="34%" valign="top" height="50px"> <b> Checked by: </b></td> 
						<td width="33%" valign="top" height="50px"> <b> Approved by: </b></td> 
					</tr>
				</table>
			</td>
		</tr>

	</table>
	<br><br>
	<table border="1" width="100%" cellpadding="3px"  id="tblMain" style="border-collapse: collapse; margin-top: 10px">
		<tr>
			<th> Machine </th> 
			<th> Process</th>
			<th> Date Started</th>
			<th> Date Ended</th>
			<th> Actual Output</th>
			<th> Operator</th>
			<th> Reject Qty</th>
			<th> Scrap Qty</th>
			<th> QC</th>
			<th> Remarks</th>
		</tr>

			<?php
				foreach($arrmrpjo_pt as $bv){
					if($rsc['ctranno']==$bv['ctranno']){
			?>

				<tr> 
					<td>&nbsp;</td>
					<td style="padding-left:10px"><?=$bv['mrp_process_desc']?> </td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>				
				</tr>
			<?php
					}
				}
			?>
		
	</table>

	<br>
	<table border="1" width="100%" style="border-collapse: collapse;">
		<tr>
			<td width="30%" valign="top" height="50px"> <b> REMARKS: </b></td> 
			<td valign="top" height="50px"> 
				<table border="0" width="100%">
					<tr>
						<th colspan="4"> RECEIVED BY </th> 
					</tr>
					<tr>
						<th width="20%" style="text-align: left"> Production: </th>
						<td>&nbsp;</td> 
						<th width="20%" style="text-align: left"> Warehouse: </th>
						<td>&nbsp;</td> 
					</tr>
					<tr>
						<th width="20%" style="text-align: left"> Date: </th>
						<td>&nbsp;</td> 
						<th width="20%" style="text-align: left"> Date: </th>
						<td>&nbsp;</td> 
					</tr>
				</table>
			</td> 
		</tr>
	</table>
<?php
	}
?>
<!-- END SUB JO PRINT -->
</body>
</html>