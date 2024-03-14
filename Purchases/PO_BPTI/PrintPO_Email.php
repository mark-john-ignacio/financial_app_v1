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

	$locsdesc = array();
	$sqlcomp = mysqli_query($con,"select * from locations where compcode='$company'");

	if(mysqli_num_rows($sqlcomp) != 0){

		while($rowcomp = mysqli_fetch_array($sqlcomp, MYSQLI_ASSOC))
		{
			$locsdesc[$rowcomp['nid']] = $rowcomp['cdesc'];
		}

	}
	
	//$csalesno = MyDec($_REQUEST['id'],$compakey);
	$csalesno = $_POST['hdntransid'];

	$cemailstoo = "";
	$cemailsccc = "";
	$cemailsbcc = "";
	$cemailsbjc = "";
	$cemailsbod = "";

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

			$cemailstoo = $row['cemailto'];
			$cemailsccc = $row['cemailcc'];
			$cemailsbcc = $row['cemailbcc'];
			$cemailsbjc = $row['cemailsubject'];
			$cemailsbod = $row['cemailbody'];

			$cApprBy = $row['capprovedby'];
			$cCheckedBy = $row['ccheckedby'];
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

	$cncost = 0;
	$costlst = "";
	foreach($roxbdyDEPLIST as $rxcost){
		$cncost++;
		if($cncost>1){
			$costlst = $costlst. "<br>";
		}

		$xcname = (isset($locsdesc[$rxcost])) ? $locsdesc[$rxcost] : $rxcost;
		$costlst = $costlst.$xcname;

	}

	$sethdr = '<table border="0" cellpadding="5px" width="100%" id="tblMain" style="border-collapse:collapse">
		<tr>
			<td style="text-align: right" colspan="8"> <font style="font-size: 18px;">PURCHASE ORDER FORM</font> </td>
		</tr>
		<tr>
			<td style="vertical-align: top; padding-top: 10px; padding-right: 5px;" colspan="2">

				<table border="0" width="100%" style="border-collapse:collapse">
					<tr>
						<td> <b>EXTERNAL PROVIDER:</b> <td>
					</tr>
					<tr>
						<td>'.$CustName.'<td>
					</tr>
					<tr>
						<td> <div style="min-height: 70px">'.$CustAdd.'</div> <td>
					</tr>
					<tr>
						<td> Phone No.: '.$contactphone.'<td>
					</tr>
					<tr>
						<td> Fax No.: '.$contactfax.'<td>
					</tr>
				</table>
				
			</td>

			<td style="vertical-align: top; padding-top: 10px; padding-left: 5px; padding-right: 5px;" colspan="2">
				<table border="0" width="100%" style="border-collapse:collapse">
					<tr>
						<td> <b>DELIVER TO:</b> <td>
					</tr>
					<tr>
						<td> '.$delto.'<td>   
					</tr>
					<tr>
						<td> <div style="min-height: 70px">'.$deladd.'</div> <td>
					</tr>
					<tr>
						<td> Phone No.: '.$delphone.'<td>
					</tr>
					<tr>
						<td> Fax No.: '.$delfax.'<td>
					</tr>
				</table>
			</td>

			<td style="vertical-align: top; padding-top: 10px; padding-left: 5px; width: 34%" colspan="4">
				<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
					<tr>
						<td> <b>PO No.</b> </td>
						<td> '.$csalesno.'</td>
					</tr>
					<tr>
						<td> <b>PR No.</b> </td>
						<td> '.implode("<br>", $roxbdyPRLIST).'</td>
					</tr>
					<tr>
						<td> <b>PAGE</b> </td>
						<td> {PAGENO} / {nbpg} </td>
					</tr>
					<tr>
						<td> <b>COST CENTER</b> </td>
						<td>'.$costlst.'</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border="1" width="100%" style="border-collapse:collapse; page-break-inside:avoid" cellpadding="5px" autosize="1">
		<tr>
			<td width="25%" align="center"> <b>REVISION NO.</b> </td>
			<td width="25%" align="center"> <b>DATE PREPARED</b> </td>
			<td width="25%" align="center"> <b>PO DUE DATE</b> </td>
			<td width="25%" align="center"> <b>PAYMENT TERMS</b> </td>
		</tr>	
		<tr>
			<td align="center"> <b>0</b> </td>
			<td align="center"> <b>'.date_format(date_create($Date), "d-M-Y").'</b> </td>
			<td align="center"> <b>'.date_format(date_create($DateNeeded), "d-M-Y").'</b> </td>
			<td align="center"> <b>'.$Terms.'</b> </td>
		</tr>			
	</table>';
	
	$setfooter = '<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
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
						<li>Inability of supplier to meet above conditions shall be a valid reason to cancel this Purchase Order without prejudice to supplier\'s interest.</li>
						<li>Delivery leadtime shall be in staggered delivery, as per required delivery date.</li>
					</ol>
				</div>
			</td>
			<td width="30%" height = "150px" valign="top">
				<div style="display: block"></b>REMARKS:</b></div>
				<div style="display: block; height: 150px; width: 100%; border: 1px solid #000"></div>
			</td>
		</tr>
	</table>
	<br>
	<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">					
		<tr>
			<td width="20%"  >';

				if($lSent==1 && $cpreparedBySign!=""){

					$setfooter = $setfooter .'<div style="text-align: center">Prepared By</div>';
					$setfooter = $setfooter .'<div style="text-align: center"><img src = "'.$cpreparedBySign.'" height="80px"></div>';
					$setfooter = $setfooter .'<div style="text-align: center">'.$cpreparedBy.'</div>';
			
				}else{
					$setfooter = $setfooter .'<div style="text-align: center">Prepared By</div>';
					$setfooter = $setfooter .'<div style="text-align: center"><img src = "white.jpg" height="80px"></div>';
					$setfooter = $setfooter .'<div style="text-align: center">'.$cpreparedBy.'</div>';
				}

			$setfooter = $setfooter .'</td>';

			$setfooter = $setfooter.'<td width="25%" align="center" height="100px" valign="top">							
			<div style="text-align: center">Approved By</div>				
			<div style="text-align: center"><img src = "white.jpg" height="80px"></div>		
			<div style="text-align: center">'.$cCheckedBy.'</div>					
			</td>';

			$setfooter = $setfooter.'<td width="20%" align="center" height="100px" valign="top">							
			<div style="text-align: center">Approved By</div>				
			<div style="text-align: center"><img src = "white.jpg" height="80px"></div>		
			<div style="text-align: center">'.$cApprBy.'</div>					
			</td>
			<td width="20%"  align="center" height="100px" valign="top">							
				<div style="padding-bottom: 60px; text-align: center">
					Supplier Confirmation
				</div>	
				<div style="text-align: center"><img src = "white.jpg" height="90px"></div>	
				<br>							
			</td>
		</tr>
	</table>';

	$html = '<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
	<tr>
		<th class="tdpadx" with="10px">No.</th>
		<th class="tdpadx">Part No.</th>
		<th class="tdpadx">Description/Size<br>Specifications</th>
		<th class="tdpadx">Item Code</th>
		<th class="tdpadx" with="80px">Qty</th>					
		<th class="tdpadx" with="50px"><b>Unit Price</b></th>
		<th class="tdpadx" with="50px">UOM</th>
		<th class="tdpadx" with="100px"><b>Amount</b></th>
	</tr>';

	if(count($roxbdy)>0){
		foreach($roxbdy as $rowdtls){
			$cnt++;
			if(floatval($rowdtls['nrate']) > 0){
				$xwithvat = 1;
			}

		$html = $html.'<tr>
			<td align="center" class="tdpadx">'.$cnt.'</td>
			<td align="center" class="tdpadx">'.$rowdtls['cpartno'].'</td>
			<td align="center" class="tdpadx">'.$rowdtls['newdesc'].'</td>
			<td align="center" class="tdpadx">'.$rowdtls['citemno'].'</td>
			<td align="center" class="tdpadx">'.intval($rowdtls['nqty']).'</td>
			<td align="right" class="tdpadx">'.number_format($rowdtls['nprice'],4).'</td>
			<td align="center" class="tdpadx">'.$rowdtls['cunit'].'</td>										
			<td align="right" class="tdpadx">'.number_format($rowdtls['namount'],2).'</td>					
		</tr>';

		} 
	}

	$html = $html.'<tr>			
			<td align="right" class="tdpadx" colspan="7"><b>TOTAL</b></td>
			<td align="right" class="tdpadx">'.number_format($Gross,2).'</td>			
		</tr>
	</table>';

	$mpdf = new \Mpdf\Mpdf([
		'mode' => '',
		'format' => 'letter',
		'default_font_size' => 8,
		'default_font' => 'Calibri',
		'margin_left' => 10,
		'margin_right' => 10,
		'margin_top' => 11,
		'margin_bottom' => 11,
		'margin_header' => 9,
		'margin_footer' => 9,
		'orientation' => 'P',
		'setAutoBottomMargin' => 'stretch',
		'setAutoTopMargin' => 'stretch',
	]);

	$mpdf->SetHTMLHeader($sethdr);
	$mpdf->SetHTMLFooter($setfooter);

	// send the captured HTML from the output buffer to the mPDF class for processing
	$mpdf->WriteHTML($html);
	$mpdf->Output('../../PDFiles/PO/'.$csalesno.'.pdf', \Mpdf\Output\Destination::FILE);

	//Redirect to sending email file

	$result = mysqli_query($con,"SELECT * FROM `parameters` where compcode='$company' and ccode in ('POEMAILBODY')"); 
            
    if (mysqli_num_rows($result)!=0) {
      while($comprow = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                 
           $emi= $comprow['cdesc']; 

		}
	}

	$cxsmsgs = "";
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