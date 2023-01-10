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

	$Conemail = $row['ccontactemail'];

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

<body>

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
		<td style="height: 5in; vertical-align: top; padding-top: 10px">

			<table border="0" width="100%">
				<tr>
					<td colspan="4" align="center" style="padding-bottom: 20px">
							<font style="font-size: 24px;">PURCHASE ORDER </font>
					</td>
				</tr>
				<tr>
					<td width="100px">
							<b>Name: </b>
					</td>
					<td>
							<?=$CustName?>
					</td>
					<td width="100px">
							<b>PO#: </b>
					</td>
					<td>
						<?=$csalesno?>    
					</td>
				</tr>

				<tr>
					<td width="100px">
							<b>Address: </b>
					</td>
					<td>
						<?=$CustAdd?>
					</td>
					<td width="100px">
							<b>PR#: </b>
					</td>
					<td>
		
					</td>
				</tr>

				<tr>
					<td width="100px">
							<b>Date: </b>
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
					<td width="100px">
							<b>Del Date: </b>
					</td>
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

<?php

$html = ob_get_contents();
ob_end_clean();


		$result = mysqli_query($con,"SELECT * FROM `parameters` where compcode='$company' and ccode in ('POEMAILBODY')"); 
            
    if (mysqli_num_rows($result)!=0) {
      while($comprow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                 
           $emi= $comprow['cdesc']; 

			}
		}

// send the captured HTML from the output buffer to the mPDF class for processing
$mpdf->WriteHTML($html);
$mpdf->Output('../../PDFiles/Quotes/'.$csalesno.'.pdf', \Mpdf\Output\Destination::FILE);

//Redirect to sending email file
	$body = $emi; 
	$subject = "Chocovron Global Corp. - Purchase Order";
 
	$email_to = $Conemail;

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
	$mail->FromName = "Chocovron Global Corp.";
	$mail->Sender = "myxfin@serttech.com"; // indicates ReturnPath header
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($email_to);

	$mail->addAttachment("../../PDFiles/Quotes/".$csalesno.".pdf");
	if(!$mail->Send()){
		echo "Mailer Error: " . $mail->ErrorInfo;
	}else{
		echo "Email Successfully Sent";
	}

?>