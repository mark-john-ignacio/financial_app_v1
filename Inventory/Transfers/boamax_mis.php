<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[$row0['nid']] = $row0['cdesc'];				
	}
	
	$cno = $_REQUEST['id'];
	$sqlhead = mysqli_query($con,"Select A.*, B.Fname, B.Minit, B.Lname from invtransfer A left join users B on A.cpreparedBy=B.Userid where compcode='$company' and ctranno='".$cno."'");
	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){

			$selwhfrom = $row['csection1'];
			$selwhto = $row['csection2'];
			$seltype = $row['ctrantype'];
			$hdremarks = $row['cremarks'];
			$hddatecnt = $row['dcutdate'];

			$seltemid = $row['template_id'];

			$lCancelled = $row['lcancelled1'];
			$lPosted = $row['lapproved1'];

			$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];

		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<style>

		body {
			font-family: Verdana, sans-serif;
			font-size: 8pt;
		}

		@page {
			size: letter portrait;
		}

		@media print {
			thead {display: table-header-group;} 			
			body {margin: 6.35mm}
		}
			
	</style>
</head>

<body >

	<center><h2> MATERIAL ISSUANCE SLIP</h2></center>
	<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
		<tr>
			<td width="20%" valign="top"> <b>Requesting Department:</b> <br><br><br> <?=$arrallsec[$selwhfrom]?></td>
			<td width="15%" valign="top"> <b>Date:</b> <br><br><br><?=date_format(date_create($hddatecnt), "F d, Y")?></td>
			<td width="20%" valign="top"> <b>Received By:</b> </td>
			<td width="20%" valign="top"> <b>Encoded By:</b> </td>
			<td width="25%" valign="top"> <b>MRS No.:</b> <?=$cno?></td>
		</tr>			
	</table>

	<table border="1" width="100%" style="border-collapse:collapse; margin-top:2px" cellpadding="5px">
		<tr>
			<th width="20" class="text-center">No.</th>
			<th width="100" class="text-center">Item Code</th>
			<th class="text-center">Part No./Part Name</th>
			<th width="70" class="text-center">Size/Spec.</th>
			<th width="70" class="text-center">Qty</th>
			<th class="text-center">Remarks</th>
		</tr>	
		<?php
			$sqlhead = mysqli_query($con,"Select A.*, B.citemdesc, B.cnotes from invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$_REQUEST['id']."' Order By A.nidentity");
			if (mysqli_num_rows($sqlhead)!=0) {
	
				$cnt = 0;
				while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
					$cnt++;
		?>
			<tr>				
				<td align="center"><?=$cnt?></td>
				<td><?=$row['citemno']?></td>
				<td><?=$row['citemdesc']?></td>
				<td><?=$row['cnotes']?></td>
				<td class="text-center"><?=number_format($row['nqty1'],2)." ".$row['cunit']?></td>
				<td><?=$row['cremarks']?></td>
			</tr>
		<?php
				}
			}
		?>
	</table>

	<table border="1" width="100%" style="border-collapse:collapse; margin-top:5px" cellpadding="5px">
		<tr>
			<td width="35%" valign="top"> <b>Requested By (Name & Signature):</b> <br><br><br><?=$cpreparedBy?></td>
			<td width="32%" valign="top"> <b>Checked By:</b> <br><br><br> </td>
			<td width="33%" valign="top"> <b>Approved By:</b> </td>
		</tr>			
	</table>

	<table border="0" width="100%" style="border-collapse: collapse; margin-top: 20px; font-size: 10px">	
		<td><?=date("h:i:sa");?> <?=date("d-m-Y");?></td>
		<td><i>Note: In Case of Error Please Report  to the concern Department within 24hrs ERASURE IS NOT ALLOWED</i></td>
		<td>BMRC-LW-001-F</td>
	</table>
</body>
</html>
 