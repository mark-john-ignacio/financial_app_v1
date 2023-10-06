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
	$sql = "select * from mrp_jo_process_t X where X.compcode='$company' and X.ctranno in (Select ctranno from mrp_jo_process where compcode='$company' and mrp_jo_ctranno  = '$tranno')";
	$resultmain = mysqli_query ($con, $sql); 
	while($row2 = mysqli_fetch_array($resultmain, MYSQLI_ASSOC)){
		$arrmrpjo_pt[] = $row2;				
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
		
	</style>
</head>

<body > <!-- onLoad="window.print()" -->

<table border="0" width="100%" cellpadding="1px"  id="tblMain">
	<tr>
		<td align="left" width="250px"> 
			<img src="<?php echo "../".$logosrc; ?>" height="68px">
		</td>
		<td align="left"> 

				<table border="0" width="100%">
						<tr>
							<td><font style="font-size: 18px;"><?php echo $logonamz; ?></font></td>
						</tr>
						<tr>
							<td><font><?php echo $logoaddrs; ?></font></td>
						</tr>
				</table>

		</td>
		<td align="center"> 
			<h1>JOB ORDER</h1>
		</td>
	</tr>
	
</table>
<br><br>

<!-- MAIN JO PRINT -->
<table border="0" width="100%" cellpadding="1px"  id="tblMain">
	<tr>
		<td width="100px"><b>Date Release: </b></td>
		<td> <?=date_format(date_create($arrmrpjo[0]['dreleasedate']), "M d, Y")?> </td>
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
	<?php
		}
	?>
</table>
<!-- END MAIN JO PRINT --> 


<!-- SUB JO PRINT -->
<?php
	foreach($arrmrpjoproc as $rsc){
?>
	<table border="0" width="100%" cellpadding="1px"  id="tblMain" style="page-break-before:always">
		<tr>
			<td width="100px"><b>Date Release: </b></td>
			<td> </td>
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
<?php
	}
?>
<!-- END SUB JO PRINT -->
</body>
</html>