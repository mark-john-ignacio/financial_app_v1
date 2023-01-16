<?php
if(!isset($_SESSION)){
session_start();


include('../../vendor/autoload.php');

require("../../vendor/phpmailer/phpmailer/src/PHPMailer.php");
require("../../vendor/phpmailer/phpmailer/src/SMTP.php");

$mpdf = new \Mpdf\Mpdf();
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
	$sqlhead = mysqli_query($con,"select a.*,b.cname, b.chouseno, b.ccity, b.cstate, C.cdesc as termdesc from quote a left join customers b on a.compcode=b.compcode and a.ccode=b.cempid left join groupings C on A.cterms = C.ccode left join users D on a.cpreparedby=D.Userid where a.compcode='$company' and a.ctranno = '$csalesno'");

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
		table.main {
			border-color: #000000;
			border-collapse: collapse;
		}
	</style>
</head>

<body>

<table border="0" width="100%" cellpadding="1px" class="main">
	<tr>
		<td style="height: 1in; border-bottom: 2px dashed #000"> 

				<table border="0" class="main">
						<tr>
							<td width="20%"><img src="<?php echo "../".$logosrc; ?>" width="136px" height="70px"></td>
							<td><font style="font-size: 9pt;"><?php echo $printhdrsrc; ?></font></td>
						</tr>
				</table>

		</td>
	</tr>
	<tr>
		<td style="height: 5in; vertical-align: top;">

			<table border="0" width="100%" class="main">

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
			
			<table border="1" border-collapse="collapse" align="center" width="100%" style="margin-top: 30px" class="main">
	
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
			
		<?php
			@$rowaaprovals = array();
			$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname from quote_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.ctranno = '$csalesno' order by a.nlevel");

			if (mysqli_num_rows($sqdts)!=0) {

				while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){
					@$rowaaprovals[] = $row;
				}
			}
		?>
			
		<table border="0" width="100%" cellspacing="20">
				<tr>
					<td width="25%" align="center">
						Prepared By:<br><br><br><br>
					</td>

					<?php
						foreach(@$rowaaprovals as $row){
					?>

						<td width="25%" align="center">
							<?php
								if(intval($row['nlevel'])==1){
									echo "Checked By:";
								}elseif(intval($row['nlevel'])==2){
									echo "Approved By:";
								}if(intval($row['nlevel'])==3){
									echo "Approved By:";
								}
							?><br><br><br><br>
						</td>
				  
					<?php
							}
					?>
					
				</tr>

				<tr>
					<td width="25%" align="center" style="border-top: 1px solid">
						<?=$cpreparedBy?>
					</td>

					<?php

							foreach(@$rowaaprovals as $row){

								$cnames = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
					?>

						<td width="25%" align="center" style="border-top: 1px solid">
							<?=$cnames;?>
						</td>
				  
					<?php
							}
					?>
					
				</tr>
			</table>

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
$mpdf->Output('../../PDFiles/Quotes/'.$csalesno.'.pdf', \Mpdf\Output\Destination::FILE);

//Redirect to sending email file
$output='<p>Dear '.$name.',</p>';
$output.='<p>This email is to notify that the QO# '.$xpono.' is waiting for your approval.</p>'; 
$output.='<p>Thanks,</p>';
$output.='<p>Myx Financials,</p>';

$body = $output; 
$subject = "Quotation";

$email_to = $email;

$fromserver = "myxfin@serttech.com"; 
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->IsSMTP();
$mail->Host = "mail.serttech.com"; // Enter your host here
$mail->SMTPAuth = true;
$mail->Username = "myxfin@serttech.com"; // Enter your email here
$mail->Password = "Sert@2022"; //Enter your password here
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->IsHTML(true);
$mail->From = "noreply@serttech.com";
$mail->FromName = "Myx Financials";
$mail->Sender = "myxfin@serttech.com"; // indicates ReturnPath header
$mail->Subject = $subject;
$mail->Body = $body;
$mail->AddAddress($email_to);

if(!$mail->Send()){
//echo "Mailer Error: " . $mail->ErrorInfo;
}else{
//echo "Email Successfully Sent";
}

?>