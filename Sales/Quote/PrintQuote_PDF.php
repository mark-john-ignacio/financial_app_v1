<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../vendor/autoload.php');

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
			$usersign[$rowusr['Userid']] = $rowusr['cusersign'];			
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
			$QuoteDate = $row['dtrandate'];
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
			$lSent= $row['lsent'];
		}
	}


	$sqldtlss = mysqli_query($con,"select A.*, B.citemdesc, B.cuserpic, B.cnotes From quote_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno = '$csalesno' Order By A.nident");


	$sethdr = "<table border=\"0\" width=\"100%\" cellpadding=\"1px\" style=\"border-collapse: collapse\">
		<tr>
			<td style=\"height: 1in; border-bottom: 2px dashed #000\"> 

				<table border=\"0\">
					<tr>
						<td width=\"20%\"><img src=\"../".$logosrc."\" width=\"136px\" height=\"70px\"></td>
						<td><font style=\"font-size: 9pt;\">".$printhdrsrc."</font></td>
					</tr>
				</table>

			</td>
		</tr>		
	</table>";

	$setfooter = "<table border=\"0\" width=\"100%\" cellpadding=\"1px\" style=\"border-collapse: collapse\"><tr><td width=\"40%\"><br><br>
	Very Truly Yours,";
			if($lSent==1 && $usersign[$cprepby] != "" && $usersign[$cprepby] != null){
				$setfooter = $setfooter . "<div><img src=\"".$usersign[$cprepby]."\" width=\"160px\" height=\"88px\"></div>";
			}else{
				$setfooter = $setfooter . "<br><br><br><br><br><br>
					<b>".$userinfo[$cprepby]."</b> 
					<br>	
					<b>".$userdept[$cprepby]."</b>
					<br>";
			}
			$setfooter = $setfooter.ucwords(strtolower($compname))."							
			</td>
			<td>
				<table border=0 width=\"80%\" align=\"center\">

						<tr>
							<td align=\"center\" colspan=\"2\"><b>Signature and Acceptance:</b></td>
						</tr>
						<tr>
							<td width=\"100px\" align=\"right\">Print Name:&nbsp;&nbsp;&nbsp;</td>
							<td style=\"border-bottom: 1px solid #000; height: 30px\">&nbsp;</td>
						</tr>
						<tr>
							<td width=\"100px\" align=\"right\">Title:&nbsp;&nbsp;&nbsp;</td>
							<td style=\"border-bottom: 1px solid #000; height: 30px\">&nbsp;</td>
						</tr>
						<tr>
							<td width=\"100px\" align=\"right\">Signature:&nbsp;&nbsp;&nbsp;</td>
							<td style=\"border-bottom: 1px solid #000; height: 30px\">&nbsp;</td>
						</tr>
				</table>
			</td>
		</tr>	
	</table><br><br><hr>".$printftrsrc;


	$html = "<table border=\"0\" width=\"100%\" cellpadding=\"1px\" style=\"border-collapse: collapse\">
		<tr>
			<td style=\"height: 30px; vertical-align: top;\">
				<b>".date_format(date_create($QuoteDate ), "F d, Y")."</b>

			</td>

			<td style=\"height: 30px; vertical-align: top; text-align: right\">
				<h4>".$csalesno."</h4>

			</td>

		</tr>
		<tr>
			<td style=\"padding-bottom: 20px\" colspan=\"2\">
			<b>".$ccontname;

			if($ccontdesg<>""){
				$html = $html . "<br>".$ccontdesg;
			}
			if($ccontdept<>""){
				$html = $html . "<br>".$ccontdept;
			}
			$html = $html . "<br>".$CustName;
			if($CustAddress<>""){
				$html = $html . "<br>".$CustAddress;
			}
			$html = $html . "<br>".$ccontemai; 

		$html = $html . "</b>							
			</td>
		</tr>
	</table>";
	
	
	$html = $html . "<table border=\"0\" width=\"100%\" cellpadding=\"1px\" style=\"border-collapse: collapse\">
		<tr>
			<td style=\"height: 30px; vertical-align: top;\" colspan=\"2\">
				<b>".$ccontsalt."</b>
			</td>
		</tr>
		<tr>
			<td style=\"height: 25px; vertical-align: top; padding-left: 30px\"  colspan=\"2\">
					This is our proposal for your requirement which includes the following:
			</td>
		</tr>
	</table>";

	$cssofr1 = "padding: 10px; color: white; background-color: gray; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid white;";

	$cssofrB = "padding: 10px; color: white; background-color: gray; border-top: 1px solid gray; border-left: 1px solid white; border-right: 1px solid white;";

	$cssofr0 = "padding: 10px; color: white; background-color: gray; border-top: 1px solid gray; border-left: 1px solid white; border-right: 1px solid gray;";

	$html = $html . "<table border=\"0\" width=\"100%\" cellpadding=\"1px\" style=\"border-collapse: collapse\">	
		<tr>
			<th style=\"".$cssofr1." width: 50px\">#</th>
			<th style=\"".$cssofrB." width: 350px; text-align: left\">Item &amp; Description</th>
			<th style=\"".$cssofrB."\">Qty</th>
			<th style=\"".$cssofrB."\">Unit Price</th>
			<th style=\"".$cssofr0."\">Total Amount</th>
		</tr>";			

		$cnt = 0;
		$ggross=0;
			while($rowdtls = mysqli_fetch_array($sqldtlss, MYSQLI_ASSOC)){ 
				$cnt++;
				$ggross = $ggross + floatval($rowdtls['namount']);
				$imgurpcsx = "";
				if($rowdtls['cuserpic']!=""){
					$imgurpcsx = "<img src='".$rowdtls['cuserpic']."' style=\"display:block;\" width=\"100px\"><br>";
				}

				$html = $html ."<tr>
				<td style=\"padding: 5px; border-spacing: 0px;border-collapse: collapse; border-top: 1px solid Gainsboro; vertical-align: top\" align=\"center\">".$cnt."</td>
				<td style=\"padding: 5px; border-spacing: 0px;border-collapse: collapse; border-top: 1px solid Gainsboro;\" align=\"left\">";
				
				if($imgurpcsx!=""){
					$html = $html . "<table border=\"0\" width=\"100%\" cellpadding=\"1px\" style=\"border-collapse: collapse\"> <tr><td width=\"100px\" style=\"vertical-align: top; text-align: right\"> ".$imgurpcsx." </td><td style=\"vertical-align: top; text-align: left; padding-left: 5px\"> ".$rowdtls['citemdesc']." <br><font color=\"#686868\"><small><i>".nl2br($rowdtls['cnotes'])."</i></small></font></br> </td> </tr></table>";
				}else{
					$html = $html . $rowdtls['citemdesc']."<br><i>".$rowdtls['cnotes']."</i>";
				}
				
				$html = $html ."</td>
				<td style=\"padding: 5px; border-spacing: 0px;border-collapse: collapse;border-top: 1px solid Gainsboro;\" align=\"center\">".number_format($rowdtls['nqty'])." ".$rowdtls['cunit']."</td>
				<td style=\"padding: 5px; border-spacing: 0px;border-collapse: collapse;border-top: 1px solid Gainsboro;\" align=\"right\">".$cCurrCode." ".number_format($rowdtls['nprice'],2)."</td>
				<td style=\"padding: 5px; border-spacing: 0px;border-collapse: collapse;border-top: 1px solid Gainsboro;\" align=\"right\">".$cCurrCode." ".number_format($rowdtls['namount'],2)."</td></tr>";

			} 
			$html = $html ."<tr> <td align=\"right\" colspan=\"5\">&nbsp;</td> </tr> <tr>
					<td align=\"right\" colspan=\"3\">&nbsp;</td>
					<td align=\"right\" style=\"padding: 5px; border: none !important; background-color: Gainsboro;\">TOTAL</td>
					<td align=\"right\" style=\"padding: 5px; background-color: Gainsboro;\">".$cCurrCode." ".number_format($ggross,2)."</td>
				</tr>
	</table>";

	$html = $html . "<table border=\"0\" style=\"border-collapse: collapse; margin-top: 10px\"> 
		<tr>
			<td><b>Terms & Conditions</b><td>
		</tr>
		<tr>
			<td style=\"padding: 2px; padding-top: 20px !important\" width=\"150px\">
				<b>Price</b>
			</td>
			<td style=\"padding: 2px; padding-top: 20px !important\">
				:&nbsp;&nbsp;Valid until <b>".date("F d, Y", strtotime($Date))."</b> only. Thereafter, all prices will be subject to our confirmation
			</td>
		</tr>
		<tr>
			<td style=\"padding: 2px;\" width=\"150px\">
				<b>Payment</b>
			</td>
			<td style=\"padding: 2px;\">:&nbsp;&nbsp;".$ctermsdesc."
				<b>".(($cvattyp=="VatEx") ? "VAT EXCLUSIVE" : "VAT INCLUSIVE")."</b>
			</td>
		</tr>
		<tr>
			<td style=\"padding: 2px;\" width=\"150px\">
				<b>Delivery</b>
			</td>
			<td style=\"padding: 2px;\">:&nbsp;&nbsp;".$cdelinfo."</td>
		</tr>
		<tr>
			<td style=\"padding: 2px;\" width=\"150px\">
				<b>Service</b>
			</td>
			<td style=\"padding: 2px;\">:&nbsp;&nbsp;".$cservinfo."</td>
		</tr>				
		<tr>
			<td style=\"padding: 2px;\" colspan=\"2\">".$Remarks."</td>
		</tr>	
	</table>";


	$mpdf = new \Mpdf\Mpdf([
		'mode' => '',
		'format' => 'A4',
		'default_font_size' => 9,
		'default_font' => 'Verdana, sans-serif',
		'margin_left' => 10,
		'margin_right' => 10,
		'margin_top' => 11,
		'margin_bottom' => 11,
		'margin_header' => 9,
		'margin_footer' => 9,
		'orientation' => 'P',
		'setAutoTopMargin' => 'stretch',
		'contenteditable'=>true,
	]);

	$mpdf->SetHTMLHeader($sethdr);

	$mpdf->WriteHTML($html.$setfooter);
	$mpdf->Output($csalesno,'I');

?>