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
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
		}

	}
	
	$csalesno = $_REQUEST['hdntransid'];

	$sqlhead = mysqli_query($con,"select a.*, b.cdesc as locname, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest a left join locations b on a.compcode=b.compcode and a.locations_id=b.nid left join users c on a.cpreparedby=c.Userid where a.compcode='$company' and a.ctranno = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$SecDesc = $row['locname'];

		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lSent = $row['lsent'];

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

<body onLoad="window.print()">

	<table border="0" width="100%" cellpadding="1px"  id="tblMain"  style="border-collapse: collapse;">
		<tr>
			<td align="center"> 

					<table border="0" width="100%">
							<tr align="center">
								<td><img src="<?php echo "../".$logosrc; ?>" height="68px"></td>
							</tr>
							<tr align="center">
								<td><font style="font-size: 18px;"><?php echo $logonamz; ?></font></td>
							</tr>
							<tr align="center">
								<td style="padding-bottom: 20px"><font><?php echo $logoaddrs; ?></font></td>
							</tr>
					</table>

			</td>
		</tr>
		<tr>
			<td style="vertical-align: top; padding-top: 10px">

				<table border="0" width="100%" style="border-collapse:collapse">
					<tr>
						<td colspan="3" align="center" style="padding-bottom: 20px">
								<font style="font-size: 24px;">PURCHASE REQUEST </font>
						</td>
					</tr>

					<tr>
						<td style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b>Date prepared:</b> <?=date("F d, Y")?></font>
						</td>

						<td style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b>Date needed:</b> <?=date_format(date_create($DateNeeded),"F d, Y")?></font>
						</td>

						<td align="right" style="padding-bottom: 10px">
						<font style="font-size: 14px;"><b> PR No.:</b> <?=$csalesno?></font>
						</td>
					</tr>


					<tr>

						<td style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b>Requested By:</b> <?=$cpreparedBy?></font>
						</td>

						<td style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b>Section:</b> <?=$SecDesc?></font>
						</td>

						<td align="right" style="padding-bottom: 10px">
							&nbsp;
						</td>

					</tr>

					<tr>

						<td style="padding-bottom: 10px" colspan="3">
							<font style="font-size: 14px;"><b>Remarks:</b> <?=$Remarks?></font>
						</td>
					
					</tr>			
				</table>

				<table border="1" align="center" width="100%" style="border-collapse: collapse;">
		
					<tr>
						<th class="tdpadx">Qty</th>
						<th class="tdpadx">Unit</th>
						<th class="tdpadx">Product Description/s</th>
					</tr>

					<?php 
					$sqlbody = mysqli_query($con,"select a.*,b.citemdesc from purchrequest_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.ctranno = '$csalesno' Order by a.nident");

					if (mysqli_num_rows($sqlbody)!=0) {

						while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
					?>

					<tr>
						<td align="center" class="tdpadx tddetz"><?php echo intval($rowdtls['nqty']);?></td>
						<td align="center" class="tdpadx tddetz"><?php echo $rowdtls['cunit'];?></td>					
						<td align="center" class="tdpadx tddetz">
							<?php 
								echo $rowdtls['citemdesc'];
								if($rowdtls['cremarks']!="" && $rowdtls['cremarks']!=null){
									echo "<br><i>".$rowdtls['cremarks']."</i>";
								}
							?>
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

				$sqdts1 = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cprno = '$csalesno' and a.nlevel=1 order by a.nlevel");

				$sqdts2 = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cprno = '$csalesno' and a.nlevel=2 order by a.nlevel");

				$sqdts3 = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchrequest_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cprno = '$csalesno' and a.nlevel=3 order by a.nlevel");
			?>
				<table border="0" width="100%" style="border-collapse: collapse;">
					<tr>
						<td align="center" width="25%">
							<b>Prepared By</b>
						</td>

						<td align="center" width="25%">
							<b><?=(mysqli_num_rows($sqdts1)!=0) ? "Checked By" : ""?></b>
						</td>

						<td align="center" width="25%">
							<b><?=(mysqli_num_rows($sqdts2)!=0) ? "Approved By" : ""?></b>
						</td>

						<td align="center" width="25%">
							<b><?=(mysqli_num_rows($sqdts3)!=0) ? "Noted By" : ""?></b>		
						</td>
					</tr>

					<tr>
						<td align="center"  valign="top">
							<?php

								if($lSent==1 && $cpreparedBySign!=""){
									echo "<div style=\"text-align: center; display: block\"><img src = '".$cpreparedBySign."?x=".time()."' width='150px'></div>";
									echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";												
								}else{
									echo "<div style=\"text-align: center; display: block; height: 50px\">&nbsp;</div>";
									echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";
								}
							?>
						</td>

						<td align="center" valign="top">
							<table border="0" width="100%" style="border-collapse: collapse;">	

								<?php
									if (mysqli_num_rows($sqdts1)!=0) {
										while($row = mysqli_fetch_array($sqdts1, MYSQLI_ASSOC)){
											$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
								?>
									<tr>
										<td align="center">
											<?php

												if($row['lapproved']==1 && $row['cusersign']!=""){
													echo "<div style=\"text-align: center; display: block\"><img src = '".$row['cusersign']."?x=".time()."' width='150px'></div>";
													echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";												
												}else{
													echo "<div style=\"text-align: center; display: block; height: 50px\">&nbsp;</div>";
													echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";
												}
											?>
										</td>
									</tr>
								<?php
										}
									}
								?>
							</table>
						</td>

						<td align="center" valign="top">
							<table border="0" width="100%" style="border-collapse: collapse;">	

								<?php
									if (mysqli_num_rows($sqdts2)!=0) {
										while($row = mysqli_fetch_array($sqdts2, MYSQLI_ASSOC)){
											$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
								?>
									<tr>
										<td align="center">
											<?php

												if($row['lapproved']==1 && $row['cusersign']!=""){
													echo "<div style=\"text-align: center; display: block\"><img src = '".$row['cusersign']."?x=".time()."' width='150px'></div>";
													echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";												
												}else{
													echo "<div style=\"text-align: center; display: block; height: 50px\">&nbsp;</div>";
													echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";
												}
											?>
										</td>
									</tr>
								<?php
										}
									}
								?>
							</table>
						</td>

						<td align="center" valign="top">
							<table border="0" width="100%" style="border-collapse: collapse;">	

								<?php
									if (mysqli_num_rows($sqdts3)!=0) {
										while($row = mysqli_fetch_array($sqdts3, MYSQLI_ASSOC)){
											$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
								?>
									<tr>
										<td align="center">
											<?php

												if($row['lapproved']==1 && $row['cusersign']!=""){
													echo "<div style=\"text-align: center; display: block\"><img src = '".$row['cusersign']."?x=".time()."' width='150px'></div>";
													echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";												
												}else{
													echo "<div style=\"text-align: center; display: block; height: 50px\">&nbsp;</div>";
													echo "<div style=\"text-align: center; display: block\">".$cpreparedBy."</div>";
												}
											?>
										</td>
									</tr>
								<?php
										}
									}
								?>
							</table>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>


</body>
</html>