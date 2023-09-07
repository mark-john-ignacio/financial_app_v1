<?php
if(!isset($_SESSION)){
session_start();


include('../../vendor/autoload.php');

$mpdf = new \Mpdf\Mpdf(['format' => 'Legal']);
ob_start();
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
	$logosrc = "";

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$compname = $rowcomp['compname'];
		}

	}

	$sqlprint = mysqli_query($con,"select * from parameters where compcode='$company' and ccode in ('QUOTEHDR','QUOTEFTR')");

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

	$sqlusers = mysqli_query($con,"select * from users");
	if(mysqli_num_rows($sqlauto) != 0){
		while($rowusr = mysqli_fetch_array($sqlusers, MYSQLI_ASSOC))
		{
			$userinfo[$rowusr['Userid']] = $rowusr['Fname']." ".$rowusr['Minit'].(($rowusr['Minit']!=="" && $rowusr['Minit']!==null) ? " " : "").$rowusr['Lname'];
			$userdept[$rowusr['Userid']] = $rowusr['cdepartment'];
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

		$cprepby =  $row['cpreparedby'];
		
		$lCancelled = $row['lcancelled'];
		$lPosted = $row['lapproved'];
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
		.tblborder {
			border-spacing: 0px;
			border-collapse: collapse;  /* <--- add this so all the internal <td>s share adjacent borders  */
			border: 1px solid black;  /* <--- so the outside of the <th> don't get missed  */
		}

		.tblhide {

				border-spacing: 0px;  /* <---- won't really need this if you have border-collapse = collapse */
				border-style: none;   /* <--- add this for no borders in the <th>s  */
		}
	</style>
</head>

<body>

<table border="0" width="100%" cellpadding="1px" style="border-collapse: collapse">
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
		<td style="min-height: 6in; max-height: 7in; vertical-align: top;">

			<table border="0" style="border-collapse: collapse" width="100%">
				<tr>
					<td style="height: 50px; vertical-align: top;">
						<b><?php echo date("F d, Y"); ?></b>

					</td>

					<td align="right">
						<h4><?=$csalesno?></h4>

					</td>

				</tr>
				<tr>
					<td style="padding-bottom: 20px" colspan="2">
						<b>
							<?php 

								echo $ccontname;
								if($ccontdesg<>""){
									echo "<br>".$ccontdesg;
								}
								if($ccontdept<>""){
									echo "<br>".$ccontdept;
								}
								echo "<br>".$CustName;
								if($CustAddress<>""){
									echo "<br>".$CustAddress;
								}
								echo "<br>".$ccontemai; 
							?>
						</b>
						
					</td>

				</tr>
				<tr>
					<td style="height: 30px; vertical-align: top;" colspan="2">
						<b>
							<?php echo $ccontsalt; ?>
						</b>
					</td>

				</tr>
				<tr>
					<td style="height: 25px; vertical-align: top; padding-left: 30px"  colspan="2">
							This is our proposal for your requirement which includes the following:
					</td>

				</tr>
			</table>
			
			<table border="0" align="center" width="95%" cellspacing="0">
	
				<tr>
					<th class="tblborder" style="padding: 3px;">ITEM</th>
					<th class="tblborder" style="padding: 3px">PRODUCT</th>
					<th class="tblborder" style="padding: 3px;">QTY</th>
					<th class="tblborder" style="padding: 3px;">UOM</th>
					<th class="tblborder" style="padding: 3px">TOTAL AMOUNT</th>
				</tr>

				<?php 
				$cnt = 0;
				$ggross=0;
					while($rowdtls = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){ 
						$cnt++;
						$ggross = $ggross + floatval($rowdtls['namount']);
				?>

				<tr>
					<td class="tblborder" style="padding: 3px" align="center"><?=$cnt?></td>
					<td class="tblborder" style="padding: 3px" align="center"><?php echo $rowdtls['citemdesc'];?></td>
					<td class="tblborder" style="padding: 3px" align="center"><?php echo $rowdtls['nqty'];?></td>
					<td class="tblborder" style="padding: 3px" align="center"><?php echo $rowdtls['cunit'];?></td>
					<td class="tblborder" style="padding: 3px" align="center">
						<?php

								if($rowdtls['cuserpic']!=""){
									echo "<img src='".$rowdtls['cuserpic']."' height='82' width='80'><br>";
								}
							?>
							<?php echo $cCurrCode." ".number_format($rowdtls['namount'],2);?>

					</td>
				</tr>

				<?php 
					} 
				?>

				<tr>
					<td class="tblhide" align="right" colspan="4" style="padding: 3px; border: none !important;"><b>TOTAL</b></td>
					<td class="tblhide" align="center" style="padding: 3px"><b><?php echo $cCurrCode." ".number_format($ggross,2);?></b></td>
				</tr>

			</table>

			<br>
			<table border="0" style="border-collapse: collapse"> 
				<tr>
					<td><b>Terms & Conditions</b><td>
				</tr>
				<tr>
					<td style="padding: 2px; padding-top: 20px !important" width="150px">
						<b>Price</b>
					</td>
					<td style="padding: 2px; padding-top: 20px !important">
						:&nbsp;Valid until <b><?php echo date("F d, Y", strtotime($Date)); ?></b> only. Thereafter, all prices will be subject to our confirmation
					</td>
				</tr>
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>Payment</b>
					</td>
					<td style="padding: 2px;">
						:&nbsp;&nbsp;<?php echo $ctermsdesc; ?>.
						<b>
						<?php 
							if($cvattyp=="VatEx"){
								echo "VAT EXCLUSIVE";
							}else{
								echo "VAT INCLUSIVE";
							}
						?>
						</b>
					</td>
				</tr>
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>Delivery</b>
					</td>
					<td style="padding: 2px;">
						:&nbsp;&nbsp;<?php echo $cdelinfo; ?>
					</td>
				</tr>
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>Service</b>
					</td>
					<td style="padding: 2px;">
							:&nbsp;&nbsp;<?php echo $cservinfo; ?>
					</td>
				</tr>				
				<tr>
					<td style="padding: 2px;" colspan="2">
						<?php echo $Remarks; ?>
					</td>
				</tr>	
			</table>

		</td>
	</tr>
	<tr>
		<td style="height: 2in; vertical-align: bottom;">
			
			
			<table border="0" width="100%">
				<tr>
					<td width="40%"><br><br>
						Very Truly Yours,<br><br><br><br><br><br>
						<b><?=$userinfo[$cprepby]?></b> 
						<br>	
						<b><?=$userdept[$cprepby]?></b>
						<br>
						<?=ucwords(strtolower($compname))?>
						
					</td>
					<td>
						<table border=0 width="80%" align="center">

								<tr>
									<td align="center" colspan="2"><b>Signature and Acceptance:</b></td>
								</tr>
								<tr>
									<td width="100px" align="right">Print Name:&nbsp;&nbsp;&nbsp;</td>
									<td style="border-bottom: 1px solid #000; height: 30px">&nbsp;</td>
								</tr>
								<tr>
									<td width="100px" align="right">Title:&nbsp;&nbsp;&nbsp;</td>
									<td style="border-bottom: 1px solid #000; height: 30px">&nbsp;</td>
								</tr>
								<tr>
									<td width="100px" align="right">Signature:&nbsp;&nbsp;&nbsp;</td>
									<td style="border-bottom: 1px solid #000; height: 30px">&nbsp;</td>
								</tr>
						</table>
					</td>
				</tr>

				
			</table>
			<br><br><hr>
			<?php echo $printftrsrc; ?>	
		</td>
	</tr>
</table>

</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();

// send the captured HTML from the output buffer to the mPDF class for processing
$mpdf->WriteHTML($html);
$mpdf->Output();

?>