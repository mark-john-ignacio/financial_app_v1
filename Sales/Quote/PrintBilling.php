<?php
if(!isset($_SESSION)){
session_start();
}

include('../../Connection/connection_string.php');
include('../../include/denied.php');

	$company = $_SESSION['companyid'];

	$sqlauto = mysqli_query($con,"select cvalue from parameters where compcode='$company' and ccode='AUTO_POST_QUOTE'");
	if(mysqli_num_rows($sqlauto) != 0){
		while($rowauto = mysqli_fetch_array($sqlauto, MYSQLI_ASSOC))
		{
			$autopost = $rowauto['cvalue'];
		}
	}

	
	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
		}

	}

	$sqlprint = mysqli_query($con,"select * from parameters where ccode in ('QUOTEHDR','QUOTEFTR')");

	if(mysqli_num_rows($sqlprint) != 0){

		while($rowprint = mysqli_fetch_array($sqlprint, MYSQLI_ASSOC))
		{
			if($rowprint['ccode']=="QUOTEHDR"){
				$printhdrsrc = $rowprint['cdesc'];
			}
			if($rowprint['ccode']=="QUOTEFTR"){
				$printftrsrc = $rowprint['cdesc'];
			}			
		}

	}
	
	$csalesno = $_REQUEST['hdntransid'];
	$sqlhead = mysqli_query($con,"select a.*,b.cname, b.chouseno, b.ccity, b.cstate, C.cdesc as termdesc from quote a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join groupings C on A.cterms = C.ccode where a.compcode='$company' and a.ctranno = '$csalesno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$CustCode = $row['ccode'];
			$CustName = $row['cname'];
			$CustAddress= "";

			if($row['chouseno']<>""){
				$CustAddress = $row['chouseno'];
			}

			if($row['ccity']<>""){
				if($CustAddress<>""){
					$CustAddress = $CustAddress.", ".$row['ccity'];
				}else{
					$CustAddress = $row['ccity'];
				}			
			}
			
			if($row['cstate']<>""){
				if($CustAddress<>""){
					$CustAddress = $CustAddress.", ".$row['cstate'];
				}else{
					$CustAddress = $row['cstate'];
				}			
			}

			$Remarks = $row['cremarks'];
			$Date = $row['dcutdate'];
			$Gross = $row['ngross'];
			$cCurrCode = $row['ccurrencycode'];

			$ccontname = $row['ccontactname'];
			$ccontdesg = $row['ccontactdesig'];
			$ccontdept = $row['ccontactdept'];
			$ccontemai = $row['ccontactemail'];
			$ccontsalt = $row['ccontactsalut'];
			$cvattyp = $row['cvattype'];
			$cterms = $row['cterms'];
			$cdelinfo = $row['cdelinfo'];
			$cservinfo = $row['cservinfo'];

			$ctermsdesc = $row['termdesc']." upon delivery";
			
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];
		}
	}


	$sqldtlss = mysqli_query($con,"select A.*, B.citemdesc, B.cuserpic, C.nrate From quote_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno left join taxcode C on B.compcode=C.compcode and B.ctaxcode=C.ctaxcode where A.compcode='$company' and A.ctranno = '$csalesno'");

	if (mysqli_num_rows($sqldtlss)!=0) {
		while($row = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){
			@$arrdtls[] = $row;
		}
	}

	$sqldtlss = mysqli_query($con,"select * From quote_t_info where compcode='$company' and ctranno = '$csalesno'");

	@$arrdtlsinfo=array();
	if (mysqli_num_rows($sqldtlss)!=0) {
		while($row = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){
			@$arrdtlsinfo[] = $row;
		}
	}

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

<body>

<table border="0" width="100%" cellpadding="1px">
	<tr>
		<td style="height: 1in; border-bottom: 2px dashed #000"> 

				<table border="0">
						<tr>
							<td width="20%"><img src="<?php echo "../".$logosrc; ?>" width="136px" height="70px"></td>
							<td><font style="font-size: 9pt;"><?php echo $printhdrsrc; ?></font></td>
						</tr>
				</table>

		</td>
	</tr>
	<tr>
		<td style="height: 5in; vertical-align: top;">

			<table border="0" width="100%">

				<tr>
					<td style="height: 50px; vertical-align: top;" align="center" colspan="4">
						<font size="6">BILLING STATEMENT</font>

					</td>

				</tr>


				<tr>
					<td style="height: 50px; vertical-align: top;" colspan="4">
						<b><?php echo date("F d, Y"); ?></b>
					</td>
				</tr>
				<tr>
					<td width="100px"> BILL TO: </td>
					<td><b><?=$CustName?><b></td>
					<td width="100px"> BILL NO.: </td>
					<td> <?=$csalesno?> </td>

				</tr>
				<tr>
					<td width="100px">&nbsp;</td>
					<td><b><?=$ccontname?><b></td>
					<td width="100px"> DUE DATE: </td>
					<td><?=date_format(date_create($Date), "F d, Y")?></td>

				</tr>

				<tr>
					<td width="100px">&nbsp;</td>
					<td><?=$ccontdesg?></td>
					<td width="100px">&nbsp;</td>
					<td>&nbsp;</td>

				</tr>

				<tr>
					<td width="100px">ADDRESS:</td>
					<td colspan="3"><?=$CustAddress?></td>
				</tr>

			</table>
			
			<table border="1" border-collapse="collapse" align="center" width="100%" style="margin-top: 30px">
	
				<tr>
					<th class="text-center" style="padding: 5px">BILL PERIOD</th>
					<th class="text-center" style="padding: 5px">DESCRIPTION</th>
					<th class="text-center" style="padding: 5px">VAT SALES</th>
					<th class="text-center" style="padding: 5px">VAT AMOUNT</th>
					<th class="text-center" style="padding: 5px">TOTAL AMOUNT</th>
				</tr>

				<?php 
				$GTVat = 0;
				$GTAMTVat = 0;
				$GTTOTAMt = 0;
					foreach(@$arrdtls as $rowdtls){

						if($cvattyp=="VatEx"){							
							$nvatamt = $rowdtls['namount'];
							$nvat=0;
							$ntotamt=$rowdtls['namount'];
						}else{
							$ntotamt=$rowdtls['namount'];
							$nvatamt=floatval($rowdtls['namount']) / (1 + (floatval($rowdtls['nrate'])/100));
							$nvat= $ntotamt - $nvatamt;							
							
						}

						$GTVat = $GTVat + $nvat;
						$GTAMTVat = $GTAMTVat + $nvatamt;
						$GTTOTAMt = $GTTOTAMt + $ntotamt;
				?>

				<tr>
					<td class="text-center" style="padding: 3px" align="center">
						<?php
							if(count(@$arrdtlsinfo)>=1){
								@$arrbilss = array();
								foreach(@$arrdtlsinfo as $rez){
									if($rez['nrefident']==$rowdtls['nident'] && $rez['citemno']==$rowdtls['citemno']){
										@$arrbilss[] = $rez['cfldnme'];
									}
								}

								if(count(@$arrbilss)>=1){
									$varx = implode("<br>", @$arrbilss);
									echo  $varx;
								}
							}
						?>
					</td>
					<td class="text-center" style="padding: 3px" align="center">
						<?php echo $rowdtls['citemdesc'];?>
							
						<?php
							if(count(@$arrdtlsinfo)>=1){
								@$arrbilss = array();
								foreach(@$arrdtlsinfo as $rez){
									if($rez['nrefident']==$rowdtls['nident'] && $rez['citemno']==$rowdtls['citemno']){
										@$arrbilss[] = $rez['cvalue'];
									}
								}

								if(count(@$arrbilss)>=1){
									$varx = implode("<br>", @$arrbilss);
									echo  "<hr>".$varx;
								}
							}
						?>

					</td>
					<td class="text-center" style="padding: 3px" align="right"><?php echo number_format($nvatamt,2) ;?></td>
					<td class="text-center" style="padding: 3px" align="right"><?php echo number_format($nvat,2) ;?></td>
					<td class="text-center" style="padding: 3px" align="right"><?php echo number_format($ntotamt,2)?></td>
				</tr>

				<?php 
					} 
				?>
				<tr>
					<td align="right" style="padding: 5px" colspan="2">TOTAL</td>
					<td align="right" style="padding: 5px"><?php echo number_format($GTAMTVat,2)?></td> 
					<td align="right" style="padding: 5px"><?php echo number_format($GTVat,2)?></td>
					<td align="right" style="padding: 5px"><?php echo number_format($GTTOTAMt,2)?></td>
				</tr>

			</table>

			<br>
			<?php echo $Remarks ;?>

		</td>
	</tr>
	<tr>
		<td style="height: 2in; vertical-align: bottom;">
			
			
		<table border="0" width="100%">
				<tr>
					<td width="25%" align="center">
						Prepared By:
					</td>
					<td width="25%" align="center">
						Checked By:
					</td>
					<td width="25%" align="center">
						Approved By:
					</td>
					<td width="25%" align="center">
						Approved By:
					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>

</body>
</html>
