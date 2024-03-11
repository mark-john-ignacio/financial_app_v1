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
	include('../../Model/helper.php');

	$company = $_SESSION['companyid'];
	$xwithvat = 0;

	$sqlcomp = mysqli_query($con,"select * from company where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$logosrc = $rowcomp['clogoname'];
			$logoaddrs = $rowcomp['compadd'];
			$logonamz = $rowcomp['compname'];
			$compakey = $rowcomp['code'];
		}

	}

	$csalesno = $_POST['hdntransid'];

	$cemailstoo = "";
	$cemailsccc = "";
	$cemailsbcc = "";
	$cemailsbjc = "";
	$cemailsbod = "";

	$sqlhead = mysqli_query($con,"select a.*, b.cname, b.chouseno, b.ccity, b.cstate, b.ccountry, b.cterms, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign, d.cdesc as termsdesc from purchase a left join suppliers b on a.compcode=b.compcode and a.ccode=b.ccode left join users c on a.cpreparedby=c.Userid left join groupings d on a.compcode=b.compcode and a.cterms=d.ccode and d.ctype='TERMS' where a.compcode='$company' and a.cpono = '$csalesno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$CustCode = $row['ccode'];
			$CustName = $row['cname'];

			$CustAdd = $row['chouseno']." ".$row['ccity']." ".$row['cstate']." ".$row['ccountry'];
			$Terms = $row['termsdesc']; 
			$CurrCode = $row['ccurrencycode'];

			$Conemail = $row['ccontactemail'];

			$Remarks = $row['cremarks'];
			$Date = $row['ddate'];
			$DateNeeded = $row['dneeded'];
			$Gross = $row['ngross'];

			$cterms = $row['cterms']; 
			$delto = $row['cdelto'];  
			$deladd = $row['ddeladd']; 
			$delinfo = $row['ddelinfo']; 
			$billto = $row['cbillto']; 
			
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];
			$lSent = $row['lsent'];

			$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
			$cpreparedBySign = $row['cusersign'];

			$cemailstoo = $row['cemailto'];
			$cemailsccc = $row['cemailcc'];
			$cemailsbcc = $row['cemailbcc'];
			$cemailsbjc = $row['cemailsubject'];
			$cemailsbod = $row['cemailbody'];
		}
	}

	$sqldtlss = mysqli_query($con,"select A.*, B.citemdesc, B.cuserpic From quote_t A left join items B on A.citemno=B.cpartno where A.compcode='$company' and A.ctranno = '$csalesno'");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

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

<body>

<?php
	if($cemailstoo!="") {
?>

<table border="0" width="100%" cellpadding="1px"  id="tblMain">
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
					<td colspan="2" align="center" style="padding-bottom: 20px">
							<font style="font-size: 24px;">PURCHASE ORDER </font>
					</td>
				</tr>

				<tr>
					<td style="padding-bottom: 10px">
						<font style="font-size: 14px;"><b>Date:</b> <?=date("F d, Y")?></font>
					</td>

					<td align="right" style="padding-bottom: 10px">
					<font style="font-size: 14px;"><b>No.:</b> <?=$csalesno?></font>
					</td>
				</tr>


				<tr>
					<td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
							<table border="0" width="100%">
								<tr>
									<td width="150px" style="padding: 10px;">
											<b>SUPPLIER'S NAME: </b>
									</td>
									<td style="padding: 10px;">
											<?=$CustName?>
											<br>
											<?=$CustAdd?>
									</td>
									<td width="100px" style="padding: 10px;">
											<b>TERMS</b>
									</td>
									<td style="padding: 10px;" align="right">
											<?=$cterms?>
											<br>
											<?=$Remarks?>
									</td>
								</tr>
							</table>
					</td>
				</tr>

				<tr>
					<td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
							<table border="0" width="100%">
								<tr>
									<td width="150px" style="padding: 10px">
										<b>DELIVERED TO: </b>									
									</td>
									<td style="padding: 10px">
										<?=$delto?>
										<br>
										<?=$deladd?>
									</td>
									
								</tr>
							</table>
					</td>
				</tr>

				<tr>
					<td colspan="2" style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
							<table border="0" width="100%">
								<tr>
									<td rowspan="2" style="padding-left: 10px;">
										<b> BILL TO: </b> <?=$billto?>
									</td>
									<td>
										<b> DELIVERY DATE: </b> <?=date_format(date_create($DateNeeded),"F d, Y");?>
									</td>
									<td rowspan="2">
										<b> REQUISITION NO. </b>
									</td>
								</tr>
								<tr>

									<td>
										<b><i>Note</i>: </b> <?=$delinfo?>
									</td>

								</tr>
							</table>
					</td>
					
					
				</tr>
				
			</table>

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
					<td align="right" class="tdpadx" style="border: 1px solid;padding-right: 10px"><b>TOTAL</b></td>
					<td align="right"  class="tdpadx" style="border: 1px solid;padding-right: 10px"><?php echo number_format($Gross,2);?></td>
					
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
									<td width="25%" align="center">
										<div style="text-align: center">Accepted By<br><br><br><br><br></div>
										<div style="text-align: center"><?=$CustName?></div>

									</td>
									<td width="25%" align="center">

										<div style="text-align: center">Prepared By</div>
										<div style="text-align: center"><div><img src="<?=$cpreparedBySign?>"></div> 

									</td>

								<?php

									$sqdts = mysqli_query($con,"select a.*, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign, a.nlevel from purchase_trans_approvals a left join users c on a.userid=c.Userid where a.compcode='$company' and a.cpono = '$csalesno' order by a.nlevel");

									if (mysqli_num_rows($sqdts)!=0) {
										while($row = mysqli_fetch_array($sqdts, MYSQLI_ASSOC)){
								?>
											<td width="25%" align="center">
												<div style="margin-bottom: 50px; text-align: center">
												<?php
													if($row['nlevel']==1){
														echo "Checked By";
													}elseif($row['nlevel']==2){
														echo "Approved By";
													}elseif($row['nlevel']==3){
														echo "Noted By";
													}
												?>
												</div>
												<div style="text-align: center"><div><img src="<?=$row['cusersign']?>"></div>

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

<?php
}else{
	echo "No Email Address (To) Detected!";
}
?>
</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();

$cxsmsgs = "";

	$result = mysqli_query($con,"SELECT * FROM `parameters` where compcode='$company' and ccode in ('POEMAILBODY')"); 
            
    if (mysqli_num_rows($result)!=0) {
      	while($comprow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                 
           $emi= $comprow['cdesc']; 

		}
	}

	// send the captured HTML from the output buffer to the mPDF class for processing
	$mpdf->WriteHTML($html);
	$mpdf->Output('../../PDFiles/PO/'.$csalesno.'.pdf', \Mpdf\Output\Destination::FILE);

	//Redirect to sending email file

	$getcred = getEmailCred();

	$body = $cemailsbod; 
	$subject = $logonamz." - ".$cemailsbjc;

	$fromserver = $getcred['cusnme']; 
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->IsSMTP();
	$mail->Host = $getcred['csmtp']; // Enter your host here
	$mail->SMTPAuth = true;
	$mail->Username = $getcred['cusnme']; // Enter your email here
	$mail->Password = $getcred['cuspass']; //Enter your password here
	$mail->SMTPSecure = $getcred['csecure'];
	$mail->Port = $getcred['cport'];
	$mail->IsHTML(true);
	$mail->From = $getcred['useremail'];
	$mail->FromName = $logonamz;
	$mail->Sender = $getcred['cusnme']; // indicates ReturnPath header
	$mail->Subject = $subject;
	$mail->CharSet = "UTF-8";
	$mail->Encoding = 'base64'; 
	$mail->Body = $body;

	$mail->addReplyTo($getcred['useremail'], $_SESSION['employeefull']);

	$array = explode(',', $cemailstoo);
	foreach($array as $value){
		$mail->AddAddress($value);
	}
	
	if($cemailsccc!=""){
		$array = explode(',', $cemailsccc);
		foreach($array as $value){
			$mail->addCC($cemailsccc); 
		}			
	}
	if($cemailsbcc!=""){
		$array = explode(',', $cemailsbcc);
		foreach($array as $value){
			$mail->addBCC($cemailsbcc); 
		}		
	}

	$mail->addAttachment("../../PDFiles/PO/".$csalesno.".pdf");

	
	if(!$mail->Send()){
		$cxsmsgs = "Mailer Error: " . $mail->ErrorInfo;

		//echo  "Mailer Error: " . $mail->ErrorInf."<br><br>";
		//print_r($mail);
	}else{
		$cxsmsgs = "Email Successfully Sent";
	}


?>


<form action="Purch_edit.php" name="frmpos" id="frmpos" method="post">
	<input type="hidden" name="txtctranno" id="txtctranno" value="<?php echo $csalesno;?>" />
</form>
<script>
	alert("<?=$cxsmsgs?>");
    document.forms['frmpos'].submit();
</script>