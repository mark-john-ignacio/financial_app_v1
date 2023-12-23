<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];
	$xwithvat = 0;

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
	$sqlhead = mysqli_query($con,"select a.*, b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign, d.cdesc as termsdesc from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join users c on a.cpreparedby=c.Userid left join groupings d on a.compcode=b.compcode and a.cterms=d.ccode and d.ctype='TERMS' where a.compcode='$company' and a.cpono = '$csalesno'");

if (mysqli_num_rows($sqlhead)!=0) {
	while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
		$CustCode = $row['ccode'];
		$CustName = $row['cname'];

		$CustAdd = $row['chouseno']." ".$row['ccity']." ".$row['cstate']." ".$row['ccountry'];
		$Terms = $row['termsdesc']; 
		$CurrCode = $row['ccurrencycode'];

		$Remarks = $row['cremarks'];
		$Date = $row['dpodate'];
		$DateNeeded = $row['dneeded'];
		$Gross = $row['ngross'];
		
		$delto = $row['cdelto'];  
		$deladd = $row['ddeladd']; 
		$delinfo = $row['ddelinfo']; 
		$billto = $row['cbillto'];   
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
		$lSent = $row['lsent'];

		$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
		$cpreparedBySign = $row['cusersign'];
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

<body >
<div style='float: right'> <font style="font-size: 18px;">PURCHASE ORDER FORM</font> </div>
<table border="0" width="100%" cellpadding="1px"  id="tblMain" style="border-collapse:collapse">
	<tr>
		<td style="vertical-align: top; padding-top: 10px; padding-right: 5px; width: 33%">

			<table border="0" width="100%" style="border-collapse:collapse">
				<tr>
					<td> <b>EXTERNAL PROVIDER:</b> <td>
				</tr>
				<tr>
					<td> <?=$CustName?> <td>
				</tr>
				<tr>
					<td> <div style="min-height: 100px"><?=$CustAdd?></div> <td>
				</tr>

			</table>
			
		</td>

		<td style="vertical-align: top; padding-top: 10px; padding-left: 5px; padding-right: 5px; width: 33%">
			<table border="0" width="100%" style="border-collapse:collapse">
				<tr>
					<td> <b>DELIVER TO:</b> <td>
				</tr>
				<tr>
					<td> <?=$delto?> <td>   
				</tr>
				<tr>
					<td> <div style="min-height: 100px"><?=$deladd?></div> <td>
				</tr>

			</table>
		</td>

		<td style="vertical-align: top; padding-top: 10px; padding-left: 5px; width: 34%">
			<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
				<tr>
					<td> <b>PO No.</b> </td>
					<td> <?=$csalesno?> </td>
				</tr>
				<tr>
					<td> <b>PR No.</b> </td>
					<td> &nbsp; </td>
				</tr>
				<tr>
					<td> <b>PAGE</b> </td>
					<td> &nbsp; </td>
				</tr>
				<tr>
					<td> <b>COST CENTER</b> </td>
					<td> &nbsp; </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="vertical-align: top; padding-top: 10px; padding-right: 5px; width: 33%" colspan='3'>

			<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
				<tr>
					<td width="25%" align="center"> <b>REVISION NO.</b> </td>
					<td width="25%" align="center"> <b>DATE PREPARED</b> </td>
					<td width="25%" align="center"> <b>PO DUE DATE</b> </td>
					<td width="25%" align="center"> <b>PAYMENT TERMS</b> </td>
				</tr>	
				<tr>
					<td align="center"> <b>0</b> </td>
					<td align="center"> <b><?=$Date?></b> </td>
					<td align="center"> <b><?=$DateNeeded?></b> </td>
					<td align="center"> <b><?=$Terms?></b> </td>
				</tr>			
			</table>
		</td>
	</tr>
	<tr>
		<td style="vertical-align: top; padding-top: 10px" colspan='3'>
			<table border="0" align="center" width="100%" style="border-collapse: collapse;">
	
				<tr>
					<th style="border: 1px solid" class="tdpadx">Qty</th>
					<th style="border: 1px solid" class="tdpadx">Unit</th>
					<th style="border: 1px solid" class="tdpadx">Product Description/s</th>
					<th style="border: 1px solid" class="tdpadx"><b>Unit Price</b></th>
					<th style="border: 1px solid" class="tdpadx"><b>Amount</b></th>
				</tr>

				<?php 
				$sqlbody = mysqli_query($con,"select a.*,b.citemdesc, a.citemdesc as newdesc from purchase_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno where a.compcode='$company' and a.cpono = '$csalesno' Order by a.nident");

				if (mysqli_num_rows($sqlbody)!=0) {

					while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
						if(floatval($rowdtls['nrate']) > 0){
							$xwithvat = 1;
						}
				?>

				<tr>
					<td align="center" class="tdpadx tddetz"><?php echo intval($rowdtls['nqty']);?></td>
					<td align="center" class="tdpadx tddetz"><?php echo $rowdtls['cunit'];?></td>					
					<td align="center" class="tdpadx tddetz"><?php echo $rowdtls['citemdesc'];?></td>
					<td align="right" class="tdpadx tddetz tdright"><?php echo number_format($rowdtls['nprice'],2);?></td>
					<td align="right" class="tdpadx tddetz tdright"><?php echo number_format($rowdtls['namount'],2);?></td>					
				</tr>

				<?php 
					} 

				}
				?>

				<tr>
					<td colspan="3" class="tdpadx" style="border-top: 1px solid; border-left: 1px solid; border-bottom: 1px solid; padding-right: 10px">
						<?php
							if($xwithvat==1){
								echo "<b><i>Note: Price inclusive of VAT</i></b>";
							}else{
								echo "<b><i>Note: Price exclusive of VAT</i></b>";
							}
						?>
					</td>
					<td align="right" class="tdpadx" style="border-top: 1px solid; border-right: 1px solid; border-bottom: 1px solid; padding-right: 10px"><b>TOTAL</b></td>
					<td align="right"  class="tdpadx" style="border: 1px solid;padding-right: 10px"><?php echo number_format($Gross,2);?></td>
					
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td style="vertical-align: bottom;"  colspan='3'>
			<br><br>	<br><br>		
			<table border="0" width="100%">
				<tr>
					<td>
						<table border=0 width="100%">
								<tr>
									<td width="25%">
										<div style="padding-bottom: 50px; text-align: center">Accepted By</div>
										<div style="text-align: center"><?=$CustName?></div>

									</td>
									<td width="25%">

									<?php
										if($lSent==1 && $cpreparedBySign!=""){
									?>
										<div style="text-align: center">Prepared By</div>
										<div style="text-align: center"><div><img src = '<?=$cpreparedBySign?>?x=<?=time()?>' ></div> 
									
										<?php
										}else{
										?>
											<div style="padding-bottom: 50px; text-align: center">Prepared By</div>
											<div style="text-align: center"><?=$cpreparedBy?></div>
										<?php
										}
										?>

									</td>

								<?php

									$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign from purchase_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cpono = '$csalesno' order by a.nlevel");

									if (mysqli_num_rows($sqdts)!=0) {
										while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){
								?>
											<td width="25%">
												<?php
													if($row['lapproved']==1 && $row['cusersign']!=""){
												?>
												<div style="text-align: center">Approved By</div>
												<div style="text-align: center"><div><img src = '<?=$row['cusersign']?>?x=<?=time()?>' ></div>
												<?php
													}else{
												?>
													<div style="padding-bottom: 50px; text-align: center">Approved By</div>
													<div style="text-align: center"><?=$row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];?></div>
												<?php
													}
												?>

											</td>

								<?php
										}
									}
								?>
								</tr>
								
						</table>
					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>


</body>
</html>