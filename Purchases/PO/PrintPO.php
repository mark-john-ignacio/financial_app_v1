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
	$sqlhead = mysqli_query($con,"select a.*, b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, b.cterms, c.Fname, c.Minit, c.Lname from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join users c on a.cpreparedby=c.Userid where a.compcode='$company' and a.cpono = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];

		$CustAdd = $row['chouseno']." ".$row['ccity']." ".$row['cstate']." ".$row['ccountry'];
		$Terms = $row['cterms']; 
		$CurrCode = $row['ccurrencycode'];

		$Remarks = $row['cremarks'];
		$Date = $row['ddate'];
		$DateNeeded = $row['dneeded'];
		$Gross = $row['ngross'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];

		$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
	}
}


$sqldtlss = mysqli_query($con,"select A.*, B.citemdesc, B.cuserpic From quote_t A left join items B on A.citemno=B.cpartno where A.compcode='$company' and A.ctranno = '$csalesno'");

?>

<!DOCTYPE html>
<html>
<head>
	<style>
		body{
			font-family: Verdana, sans-serif;
			font-size: 10pt;
		}
		table {
			border-color: #000000;
			border-collapse: collapse;
		}
		
	</style>
</head>

<body >

<table border="0" width="100%" cellpadding="1px"  id="tblMain">
	<tr>
		<td align="center"> 

				<table border="0" width="100%">
						<tr align="center">
							<td><img src="<?php echo "../".$logosrc; ?>" width="80px" height="68px"></td>
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

			<table border="1" width="100%">
				<tr>
					<td colspan="4" align="center" style="padding-bottom: 20px">
							<font style="font-size: 24px;">PURCHASE ORDER </font>
					</td>
				</tr>

				<tr>
					<td colspan="2">
					<font style="font-size: 14px;"><b>Date:</b> <?=date("F d, Y")?></font>
					</td>

					<td colspan="2" align="right">
					<font style="font-size: 14px;"><b>No.:</b> <?=$csalesno?></font>
					</td>
				</tr>


				<tr>
					<td width="200px">
							<b>SUPPLIER'S NAME: </b>
					</td>
					<td>
							<?=$CustName?>
					</td>
					<td width="100px">
							<b>TERMS </b>
					</td>
					<td rowspan="2">
						    
					</td>
				</tr>

				<tr>
					<td width="100px">
							&nbsp;
					</td>
					<td>
						<?=$CustAdd?>
					</td>
					<td width="100px">
							
					</td>

				</tr>

				<tr>
					<td width="200px">
							<b>DELIVERED TO: </b>
					</td>
					<td>
						<?=date_format(date_create($Date), "M d, Y H:i:s")?>
					</td>
					<td width="100px">
							<b>Our Ref: </b>
					</td>
					<td>
		
					</td>
				</tr>

				<tr>
					<td width="100px">&nbsp;</td>
					<td>
						<?=date_format(date_create($DateNeeded), "M d, Y")?>
					</td>
					<td width="100px">
							<b>Terms: </b>
					</td>
					<td>
						<?=$Terms?>
					</td>
				</tr>
				
			</table>
			<br>
			<table border="0" border-collapse="collapse" align="center" width="95%">
	
				<tr>
					<th style="padding: 3px; border-bottom: 1px solid">Qty</th>
					<th style="padding: 3px; border-bottom: 1px solid">Unit</th>
					<th style="padding: 3px; border-bottom: 1px solid">Product Description/s</th>
					<td style="padding: 3px; border-bottom: 1px solid" align="right"><b>Unit Price</b></td>
					<td style="padding: 3px; border-bottom: 1px solid" align="right"><b>Amount</b></td>
				</tr>

				<?php 
				$sqlbody = mysqli_query($con,"select a.*,b.citemdesc, a.citemdesc as newdesc from purchase_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.cpono = '$csalesno' Order by a.nident");

				if (mysqli_num_rows($sqlbody)!=0) {

					while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
				?>

				<tr>
					<td style="padding: 3px"><?php echo $rowdtls['nqty'];?></td>
					<td style="padding: 3px"><?php echo $rowdtls['cunit'];?></td>					
					<td style="padding: 3px"><?php echo $rowdtls['citemdesc'];?></td>
					<td style="padding: 3px" align="right"><?php echo number_format($rowdtls['nprice'],2);?></td>
					<td style="padding: 3px" align="right"><?php echo number_format($rowdtls['namount'],2) . " " . $CurrCode;?></td>
					
				</tr>

				<?php 
					} 

				}
				?>

				<tr>
					<td colspan="4" style="padding-top: 10px" align="right"><b>Total Amount</b></td>
					<td style="padding-top: 10px" align="right"><?php echo number_format($Gross,2) . " " . $CurrCode;?></td>
					
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td style="vertical-align: bottom;">
			<br><br>	<br><br>		
			<table border="0" width="100%">
				<tr>
					<td>
						<table border=0 width="100%">
								<tr>
									<td width="33%">

										<div style="text-align: center; width: 95%">
											<div><?=$cpreparedBy?></div>
										</div>

										<div style="text-align: center; border-top: 1px solid; width: 95%">
											<div class="cols-xs-3">Prepared By</div>
										</div>

									</td>

									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>

								<?php

									$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname from purchase_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cpono = '$csalesno' order by a.nlevel");

									if (mysqli_num_rows($sqdts)!=0) {
										$nvevel = 0;
										$cntr=0;
										while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){
											if($nvevel!=$row['nlevel']){
												$cntr++;
												$nvevel=$row['nlevel'];

												if($cntr>1){
													echo "</tr>";
												}
												echo "<tr>";
											}
								?>
											<td width="33%">

												<div style="text-align: center; width: 95%">
													<div><br><br>Approved By:<br><br><br><br></div>
												</div>

												<div style="text-align: center; width: 95%">
													<div><?=$row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];?></div>
												</div>

												<div style="text-align: center; border-top: 1px solid; width: 95%">
													<div class="cols-xs-3">Authorized Signature/Date</div>
												</div>

											</td>

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
