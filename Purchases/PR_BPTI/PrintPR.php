<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
			$companylogx = $rowcomp['clogoname'];
		}

	}
	
	$csalesno = $_REQUEST['hdntransid'];

	$sqlhead = mysqli_query($con,"select a.*, b.cdesc as locname, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign, d.cdesc as creqname from purchrequest a left join locations b on a.compcode=b.compcode and a.locations_id=b.nid left join users c on a.cpreparedby=c.Userid left join mrp_operators d on a.compcode=d.compcode and a.crequestedby=d.nid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$SecDesc = $row['locname'];

		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lSent = $row['lsent'];

		$cApprvBy = $row['capprovedby'];
		$cCheckedBy = $row['ccheckedby'];

		$cReqBy = $row['creqname'] ;

		$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
		$cpreparedBySign = $row['cusersign'];
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Arial, sans-serif;
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

<body onLoad="window.print()">
	<div id="imgcontent">
		<img src="../<?=$companylogx?>" class="ribbon" alt="" width="150px"/>
	</div>

	<table border="0" width="100%" cellpadding="1px"  id="tblMain"  style="border-collapse: collapse;">
		<tr>
			<td style="vertical-align: top;">

				<table border="0" width="100%" style="border-collapse:collapse">
					<tr>
						<td colspan="3" align="center" height="50px">
							<font style="font-size: 24px;">PURCHASE REQUISITION SLIP </font>
						</td>
					</tr>

					<tr>
						<td style="padding-bottom: 10px; padding-top: 10px">
							<font style="font-size: 14px;"><b>Department:</b> <?=$SecDesc?></font>
						</td>

						<td align="right" style="padding-bottom: 10px; padding-top: 10px">
							<font style="font-size: 14px;"><b>Date prepared:</b> <?=date("F d, Y")?></font>
						</td>

						
					</tr>


					<tr>
						<td style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b>Date needed:</b> <?=date_format(date_create($DateNeeded),"F d, Y")?></font>
						</td>
						<td align="right" style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b> PR No.:</b> <?=$csalesno?></font>
						</td>

					</tr>
							
				</table>

				<table border="1" align="center" width="100%" style="border-collapse: collapse;">
		
					<tr>
						<th class="tdpadx">No.</th>
						<th class="tdpadx">Part No.</th>
						<th class="tdpadx">Description/Size</th>
						<th class="tdpadx">Item Code</th>
						<th class="tdpadx">Qty</th>
						<th class="tdpadx">Unit</th>
						<th class="tdpadx">Remarks</th>
					</tr>

					<?php 
					$cnt = 0;
					$sqlbody = mysqli_query($con,"select a.*, IFNULL(c.cdesc,'') as locdesc from purchrequest_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join locations c on a.compcode=c.compcode and a.location_id=c.nid where a.compcode='$company' and a.ctranno = '$csalesno' Order by a.nident");

					if (mysqli_num_rows($sqlbody)!=0) {
						while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
							$cnt++;
					?>

					<tr>
						<td align="center" class="tdpadx tddetz"><?=$cnt;?></td>
						<td align="center" class="tdpadx tddetz"><?=$rowdtls['cpartdesc']?></td>
						<td align="center" class="tdpadx tddetz"><?=$rowdtls['citemdesc']?></td>
						<td align="center" class="tdpadx tddetz"><?=$rowdtls['citemno']?></td>
						<td align="center" class="tdpadx tddetz">
							<?php 
								if(floor( $rowdtls['nqty'] ) != $rowdtls['nqty']){
									echo number_format($rowdtls['nqty']);
								}else{
									echo number_format($rowdtls['nqty'],2);
								}
							?>
						</td>
						<td align="center" class="tdpadx tddetz"><?php echo $rowdtls['cunit'];?></td>					
						<td align="center" class="tdpadx tddetz">
							<?=$rowdtls['cremarks']?> 
							<?=($rowdtls['cremarks']!="" && $rowdtls['locdesc']!="") ? "<br>" : ""?>
							<?=($rowdtls['locdesc']!="") ? $rowdtls['locdesc'] : ""?>
						</td>
						
					</tr>

					<?php 
						} 

					}
					?>

				</table>
			</td>
		</tr>
		<tr>
			<td style="vertical-align: bottom; padding-top: 50px">

			<?php
				//get approvals 2nd and 3rd
				$appnmbr = 1;

				$sqdts1 = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cprno = '$csalesno' and a.nlevel=1 order by a.nlevel");

				if (mysqli_num_rows($sqdts1)!=0) {
					$appnmbr++;
				}

				$sqdts2 = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cprno = '$csalesno' and a.nlevel=2 order by a.nlevel");

				if (mysqli_num_rows($sqdts2)!=0) {
					$appnmbr++;
				}

				$sqdts3 = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cprno = '$csalesno' and a.nlevel=3 order by a.nlevel");

				if (mysqli_num_rows($sqdts3)!=0) {
					$appnmbr++;
				}
  
			?>
				<table border="1" width="100%" style="border-collapse: collapse;">
					<tr>
						<td align="center" width="33%">
							<b>Requested By</b>
						</td>
						<td align="center" width="33%">
							<b>Checked By</b>
						</td>
						<td align="center">
							<b>Approved By</b>
						</td>
					</tr>

					<tr>
						<td align="center"  valign="top">
							<?php

								//if($lSent==1 && $cpreparedBySign!=""){
								//	echo "<div style=\"text-align: center; display: block\"><img src = '".$cpreparedBySign."?x=".time()."' height='80px'></div>";
								//	echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";												
								//}else{
									echo "<div style=\"text-align: center; display: block; height: 40px\">&nbsp;</div>";
									echo "<div style=\"text-align: center; display: block\">".$cReqBy."</div>";
								//}
							?>
						</td>

						
						<td align="center" valign="top">
							<div style="text-align: center; display: block; height: 40px">&nbsp;</div>
							<div style="text-align: center; display: block\"><?=$cCheckedBy?></div>												
						</td>
						<td align="center" valign="top">
							<div style="text-align: center; display: block; height: 40px">&nbsp;</div>
							<div style="text-align: center; display: block\"><?=$cApprvBy?></div>												
						</td>						
					</tr>
				</table>

			</td>
		</tr>
	</table>

	<table border="0" width="100%" style="border-collapse: collapse; margin-top: 20px; font-size: 10px">	
		<td><?=date("h:i:sa");?> <?=date("d-m-Y");?></td>
		<td><i>Note: In Case of Error Please Report  to the concern Department within 24hrs ERASURE IS NOT ALLOWED</i></td>
		<td>BMRC-PC-001-F</td>
	</table>

</body>
</html>