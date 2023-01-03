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
		<td style="height: 7in; vertical-align: top;">

			<table border="0">
				<tr>
					<td style="height: 50px; vertical-align: top;">
						<b><?php echo date("F d, Y"); ?></b>

					</td>

				</tr>
				<tr>
					<td style="padding-bottom: 20px">
						<b>
							<?php 

								echo $ccontname."<br>".$ccontdesg."<br>".$ccontdept."<br>".$CustName;
								if($CustAddress<>""){
									echo "<br>".$CustAddress;
								}
								echo "<br>".$ccontemai; 
							?>
						</b>
						
					</td>

				</tr>
				<tr>
					<td style="height: 30px; vertical-align: top;">
						<b>
							<?php echo $ccontsalt; ?>
						</b>
					</td>

				</tr>
				<tr>
					<td style="height: 25px; vertical-align: top; padding-left: 30px">
							This is our proposal for your requirement which includes the following:
					</td>

				</tr>
			</table>
			
			<table border="1" border-collapse="collapse" align="center" width="95%">
	
				<tr>
					<th class="text-center" style="padding: 3px">Qty</th>
					<th class="text-center" style="padding: 3px">Product Description/s</th>
					<th class="text-center" style="padding: 3px">Unit Price</th>
				</tr>

				<?php 
					while($rowdtls = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){ 
				?>

				<tr>
					<td class="text-center" style="padding: 3px" align="center"><?php echo $rowdtls['nqty']. " " . $rowdtls['cunit'];?></td>
					<td class="text-center" style="padding: 3px" align="center"><?php echo $rowdtls['citemdesc'];?></td>
					<td class="text-center" style="padding: 3px" align="center">
						<?php

								if($rowdtls['cuserpic']!=""){
									echo "<img src='".$rowdtls['cuserpic']."' height='82' width='80'><br>";
								}
							?>
							<?php echo $cCurrCode." ".$rowdtls['nprice'];?>

					</td>
				</tr>

				<?php 
					} 
				?>

			</table>

			<br>
			<table border="0">
				<tr>
					<td style="padding: 2px; padding-top: 20px !important" width="150px">
						<b>PRICE</b>
					</td>
					<td style="padding: 2px; padding-top: 20px !important">
						:&nbsp;
						<?php 
							if($cvattyp=="VatEx"){
								echo "VAT EXCLUSIVE";
							}else{
								echo "VAT INCLUSIVE";
							}
						?>
					</td>
				</tr>
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>PAYMENT</b>
					</td>
					<td style="padding: 2px;">
						:&nbsp;&nbsp;<?php echo $ctermsdesc; ?>
					</td>
				</tr>
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>DELIVERY</b>
					</td>
					<td style="padding: 2px;">
						:&nbsp;&nbsp;<?php echo $cdelinfo; ?>
					</td>
				</tr>
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>SERVICE</b>
					</td>
					<td style="padding: 2px;">
						:&nbsp;&nbsp;<?php echo $cservinfo; ?>
					</td>
				</tr>				
				<tr>
					<td style="padding: 2px;" width="150px">
						<b>PRICE VALIDTY</b>
					</td>
					<td style="padding: 2px;">
						:&nbsp;&nbsp;<?php echo date("F d, Y", strtotime($Date)); ?>
					</td>
				</tr>
				<tr>
					<td style="padding: 2px; padding-top: 20px !important; vertical-align: bottom;" colspan="2">
						<br>
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
					<td width="40%">
						<?php echo $printftrsrc; ?>	
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
	$body = "Sending You The Quote"; 
	$subject = "Myx Financials - Quotation Sending";
 
	$email_to = 'mhaitz.endriga@gmail.com';

	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->IsSMTP();
	//$mail->SMTPDebug = 3;
	//$mail->Debugoutput = 'html';

	$mail->Host = "smtp.gmail.com"; // Enter your host here
	$mail->SMTPAuth = true;
	$mail->Username = "myxwebportal@gmail.com"; // Enter your email here
	$mail->Password = "?May052486..."; //Enter your password here
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';

	$mail->IsHTML(true);
	$mail->SetFrom("myxwebportal@gmail.com","Myx Financials");
	$mail->addReplyTo("myxwebportal@gmail.com","Myx Financials");

	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($email_to);
	$mail->AddCC('maita.galang@gmail.com','Sert Guro');
	$mail->addAttachment("../../PDFiles/Quotes/".$csalesno.".pdf");
	if(!$mail->Send()){
		echo "Mailer Error: " . $mail->ErrorInfo;
	}else{
		echo "Email Successfully Sent";
	}

?>