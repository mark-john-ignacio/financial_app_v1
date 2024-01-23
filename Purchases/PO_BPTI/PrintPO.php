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

	$locsdesc = array();
	$sqlcomp = mysqli_query($con,"select * from locations where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){
		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$locsdesc[$rowcomp['nid']] = $rowcomp['cdesc'];
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

			$contactphone = $row['ccontactphone'];
			$contactfax = $row['ccontactfax'];
			
			$delto = $row['cdelto'];  
			$deladd = $row['ddeladd']; 
			$delemail = $row['ddelemail'];
			$delphone = $row['ddelphone'];
			$delfax = $row['ddelfax'];
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

	$sqlbody = mysqli_query($con,"select a.*,b.citemdesc, a.citemdesc as newdesc, c.locations_id from purchase_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join purchrequest c on a.compcode=c.compcode and a.creference=c.ctranno where a.compcode='$company' and a.cpono = '$csalesno' Order by a.nident");

	$roxbdy = array();
	$roxbdyPRLIST = array();
	$roxbdyDEPLIST = array();
	if (mysqli_num_rows($sqlbody)!=0) {
		$cnt = 0;
		while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){
			$roxbdy[] = $rowdtls;
			if(!in_array($rowdtls['creference'], $roxbdyPRLIST)){
				if($rowdtls['creference']!="" && $rowdtls['creference']!=null){
					$roxbdyPRLIST[] = $rowdtls['creference'];
				}
			}

			if(!in_array($rowdtls['locations_id'], $roxbdyDEPLIST)){
				if($rowdtls['locations_id']!="" && $rowdtls['locations_id']!=null){
					$roxbdyDEPLIST[] = $rowdtls['locations_id'];
				}
			}
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

		.tdpadx{
			border: 1px solid #000;
		}

		.page-header-space {
			height: 250px;
		}

		.page-footer-space {
			height: 250px;
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

<body onLoad="window.print()">

	<table>
  		<thead><tr><td>
			<div class="page-header-space">
				<table border="0" cellpadding="5px" width="100%" id="tblMain" style="border-collapse:collapse">
					<tr>
						<td style="text-align: right" colspan='8'> <font style="font-size: 18px;">PURCHASE ORDER FORM</font> </td>
					</tr>
					<tr>
						<td style="vertical-align: top; padding-top: 10px; padding-right: 5px;" colspan='2'>

							<table border="0" width="100%" style="border-collapse:collapse">
								<tr>
									<td> <b>EXTERNAL PROVIDER:</b> <td>
								</tr>
								<tr>
									<td> <?=$CustName?> <td>
								</tr>
								<tr>
									<td> <div style="min-height: 70px"><?=$CustAdd?></div> <td>
								</tr>
								<tr>
									<td> Phone No.: <?=$contactphone?> <td>
								</tr>
								<tr>
									<td> Fax No.: <?=$contactfax?> <td>
								</tr>


							</table>
							
						</td>

						<td style="vertical-align: top; padding-top: 10px; padding-left: 5px; padding-right: 5px;" colspan='2'>
							<table border="0" width="100%" style="border-collapse:collapse">
								<tr>
									<td> <b>DELIVER TO:</b> <td>
								</tr>
								<tr>
									<td> <?=$delto?> <td>   
								</tr>
								<tr>
									<td> <div style="min-height: 70px"><?=$deladd?></div> <td>
								</tr>
								<tr>
									<td> Phone No.: <?=$delphone?> <td>
								</tr>
								<tr>
									<td> Fax No.: <?=$delfax?> <td>
								</tr>
							</table>
						</td>

						<td style="vertical-align: top; padding-top: 10px; padding-left: 5px; width: 34%" colspan='4'>
							<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
								<tr>
									<td> <b>PO No.</b> </td>
									<td> <?=$csalesno?> </td>
								</tr>
								<tr>
									<td> <b>PR No.</b> </td>
									<td> <?=implode("<br>", $roxbdyPRLIST)?> </td>
								</tr>
								<tr>
									<td> <b>PAGE</b> </td>
									<td> <div class="page-number"></div> </td>
								</tr>
								<tr>
									<td> <b>COST CENTER</b> </td>
									<td> 
										<?php
											$cncost = 0;
											foreach($roxbdyDEPLIST as $rxcost){
												$cncost++;
												if($cncost>1){
													echo "<br>";
												}

												echo (isset($locsdesc[$rxcost])) ? $locsdesc[$rxcost] : $rxcost;
											}
										?> 
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
					<tr>
						<td width="25%" align="center"> <b>REVISION NO.</b> </td>
						<td width="25%" align="center"> <b>DATE PREPARED</b> </td>
						<td width="25%" align="center"> <b>PO DUE DATE</b> </td>
						<td width="25%" align="center"> <b>PAYMENT TERMS</b> </td>
					</tr>	
					<tr>
						<td align="center"> <b>0</b> </td>
						<td align="center"> <b><?=date_format(date_create($Date), "d-M-Y")?></b> </td>
						<td align="center"> <b><?=date_format(date_create($DateNeeded), "d-M-Y")?></b> </td>
						<td align="center"> <b><?=$Terms?></b> </td>
					</tr>			
				</table>
			</div>
		</td></tr></thead>

		<tbody><tr><td>
    		<div class="page">
				<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
					<tr>
						<th class="tdpadx" with="10px">No.</th>
						<th class="tdpadx">Part No.</th>
						<th class="tdpadx">Description/Size<br>Specifications</th>
						<th class="tdpadx">Item Code</th>
						<th class="tdpadx" with="80px">Qty</th>					
						<th class="tdpadx" with="50px"><b>Unit Price</b></th>
						<th class="tdpadx" with="50px">UOM</th>
						<th class="tdpadx" with="100px"><b>Amount</b></th>
					</tr>

					<?php
					if(count($roxbdy)>0){
						foreach($roxbdy as $rowdtls){
							$cnt++;
							if(floatval($rowdtls['nrate']) > 0){
								$xwithvat = 1;
							}
					?>

					<tr>
						<td align="center" class="tdpadx"><?=$cnt?></td>
						<td align="center" class="tdpadx"><?=$rowdtls['cpartno'];?></td>
						<td align="center" class="tdpadx"><?php echo $rowdtls['newdesc'];?></td>
						<td align="center" class="tdpadx"><?=$rowdtls['citemno'];?></td>
						<td align="center" class="tdpadx"><?php echo intval($rowdtls['nqty']);?></td>
						<td align="right" class="tdpadx"><?php echo number_format($rowdtls['nprice'],4);?></td>
						<td align="center" class="tdpadx"><?php echo $rowdtls['cunit'];?></td>										
						<td align="right" class="tdpadx"><?php echo number_format($rowdtls['namount'],2);?></td>					
					</tr>

					<?php 
						} 
					}
					?>

					<tr>
						
						<td align="right" class="tdpadx" colspan="7"><b>TOTAL</b></td>
						<td align="right" class="tdpadx"><?php echo number_format($Gross,2);?></td>
						
					</tr>
				</table>
			</div>
		</td></tr></tbody>


		<tr><td>
			<div class="footer-space">
				<table border="0" width="100%" style="border-collapse:collapse" cellpadding="5px">
					<tr>
						<td width="70%" valign="top">
							<div style="display: block"></b>Conditions:</b></div>
							<div style="display: block;">
								<ol style="padding-left: 20px;">
									<li>Item to be delivered must comply to quality requiremnts.</li>
									<li>The company has the right to reject items found defective and not in accordance with the required specifications.</li>
									<li>Daily interest of __% shall be charged on all delayed deliveries, including cancellation of order contractor or production for services will pay for all the damages incurred by BPTI caused by failure to complete project within  he agreed.</li>
									<li>Processing of payment shall commence only upon submission of complete documents i.e. sales invoice, delivery reciept, installation, order, etc., of the purchase terms.</li>
									<li>Payments shall be released only upon issuance of official receipt by the supplier.</li>
									<li>Inability of supplier to meet above conditions shall be a valid reason to cancel this Purchase Order without prejudice to supplier's interest.</li>
									<li>Delivery leadtime shall be in staggered delivery, as per required delivery date.</li>
								</ol>
							</div>
						</td>
						<td width="30%">
							<div style="display: block"></b>REMARKS:</b></div>
							<div style="display: block; height: 150px; width: 100%; border: 1px solid #000"></div>
						</td>
					</tr>
				</table>
					
				<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">					
					<tr>
						<td height="50px">
							<?php
								if($lSent==1 && $cpreparedBySign!=""){
							?>
									<div style="text-align: center">Prepared By</div>
									<div style="text-align: center"><div><img src = '<?=$cpreparedBySign?>?x=<?=time()?>' height="80px"></div> 
									<div style="text-align: center"><?=$cpreparedBy?></div> 
								
								<?php
								}else{
								?>
									<div style="text-align: center">Prepared By</div>
									<div style="text-align: center"><div style="height:80px">&nbsp;</div></div>
									<div style="text-align: center"><?=$cpreparedBy?></div>
							<?php
								}
							?>
						</td>

						<?php

							$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign,a.nlevel from purchase_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cpono = '$csalesno' order by a.nlevel");

							if (mysqli_num_rows($sqdts)!=0) {
								while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){
						?>
							<td width="25%" height="50px">
								<?php
									if($row['lapproved']==1 && $row['cusersign']!=""){
								?>
								<div style="text-align: center">
										<?php
											if($row['nlevel']==1){
												echo "Checked By";
											}elseif($row['nlevel']==2 || $row['nlevel']==3){
												echo "Approved By";
											}
										?>
								</div>
								<div style="text-align: center"><div><img src = '<?=$row['cusersign']?>?x=<?=time()?>' ></div>
								<?php
									}else{
								?>
									<div style="text-align: center">
										<?php
											if($row['nlevel']==1){
												echo "Checked By";
											}elseif($row['nlevel']==2 || $row['nlevel']==3){
												echo "Approved By";
											}
										?>
									</div>
									<div style="text-align: center"><div style="height:80px">&nbsp;</div> 
									<div style="text-align: center"><?=$row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];?></div>
								<?php
									}
								?>

							</td>
						<?php
								}
							}else{
						?>
							<td width="25%">							
								<div style="text-align: center">
									Checked By
								</div>	
								<div style="text-align: center"><div style="height:90px">&nbsp;</div></div>						
							</td>
							<td width="25%">							
								<div style="text-align: center">
									Approved By
								</div>	
								<div style="text-align: center"><div style="height:90px">&nbsp;</div></div>							
							</td>
						<?php
							}
						?>

						<td width="25%">							
							<div style="text-align: center">
								Supplier Confirmation
							</div>	
							<div style="text-align: center"><div style="height:90px">&nbsp;</div>							
						</td>
					</tr>
				</table>
						
				
			</div>
		</td></tr>
	</table>

</body>
</html>
 