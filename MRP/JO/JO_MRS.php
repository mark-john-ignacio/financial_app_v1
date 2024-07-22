<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../vendor/autoload.php');

	// Set a simple Footer including the page number
	//$mpdf->setFooter('{PAGENO}');

	//ob_start();

	$mpdf = new \Mpdf\Mpdf([
		'mode' => '',
		'format' => [215.9, 139.7],
		'default_font_size' => 9,
		'default_font' => 'Arial, sans-serif',
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

	$arrallsec = array();
	$sqlempsec = mysqli_query($con,"select A.nid, A.cdesc From locations A Where A.compcode='$company' and A.cstatus='ACTIVE' Order By A.cdesc");
	$rowdetloc = $sqlempsec->fetch_all(MYSQLI_ASSOC);
	foreach($rowdetloc as $row0){
		$arrallsec[$row0['nid']] = $row0['cdesc'];				
	}
	
	$cno = $_REQUEST['txtx'];

	//echo "Select A.*, B.Fname, B.Minit, B.Lname from invtransfer A left join users B on A.cpreparedBy=B.Userid where compcode='$company' and mrp_jo_ctranno='".$cno."'";

	$sqlhead = mysqli_query($con,"Select A.*, B.Fname, B.Minit, B.Lname from invtransfer A left join users B on A.cpreparedBy=B.Userid where compcode='$company' and mrp_jo_ctranno='".$cno."' and A.lcancelled1=0");
	if (mysqli_num_rows($sqlhead)!=0) {
		$xccnt = 0;
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){


			$sethdr = "";
			$setfooter = "";
			$html = "";
			$xccnt++;

			$selwhfrom = $row['csection1'];
			$selwhto = $row['csection2'];
			$seltype = $row['ctrantype'];
			$hdremarks = $row['cremarks'];
			$hddatecnt = $row['dcutdate'];

			$seltemid = $row['template_id'];

			$lCancelled = $row['lcancelled1'];
			$lPosted = $row['lapproved1'];

			$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
	

			$sethdr = '<table border="0" cellpadding="0" width="100%" id="tblMain" style="border-collapse:collapse">
				<tr>
					<td height="50px" width="100px"> <img src="../'.$logosrc.'" width="100px"/> </td>
					<td style="text-align: center" height="50px"> <font style="font-size: 18px;">MATERIAL REQUISITION SLIP</font> </td>
					<td height="50px" width="100px" style="text-align: right"> {PAGENO} / {nbpg} </td>
				</tr>
				<tr>
					<td style="vertical-align: top; padding-top: 10px; padding-right: 5px;" colspan="3">

						<table border="1" width="100%" style="border-collapse:collapse" cellpadding="5px">
							<tr>
								<td width="20%" valign="top"> <b>Date Requested:</b> <br><br><br>'.date_format(date_create($hddatecnt), "F d, Y").'</td>
								<td width="25%" valign="top"> <b>Requesting Department:</b> <br><br><br> '.$arrallsec[$selwhfrom].'</td>
								<td width="30%" valign="top"> <b>MRS Received By Date ('.$arrallsec[$selwhto].'):</b> </td>
								<td width="25%" valign="top"> <b>MRS No.:</b> '.$row['ctranno'].'<br><br><b>Model No.: </b>'.$hdremarks.'<br></td>
							</tr>
						</table>
						
					</td>
				</tr>
			</table>';
	
			$setfooter = '<table border="1" width="100%" style="border-collapse:collapse; margin-top:5px" cellpadding="5px">
			<tr>
				<td width="35%" valign="top"> <b>Requested By (Name & Signature):</b> <br><br><br>&nbsp;</td>
				<td width="32%" valign="top"> <b>Checked By:</b> <br><br><br> </td>
				<td width="33%" valign="top"> <b>Approved By:</b> </td>
			</tr></table><table border="0" width="100%" style="border-collapse:collapse; font-size: 10px"><tr><td> '.date("h:i:sa") . ' '. date("d-m-Y").' </td><td> Note: In Case of Error Please Report to the concern Department within 24hrs ERASURE IS NOT ALLOWED </td><td> BMRC-LW-001-F </td></tr></table>';
			//end foot

			$html = '<table border="1" align="center" width="100%" style="border-collapse: collapse;">
			<thead>
			<tr>
				<th width="50px" class="text-center">No.</th>
				<th width="100px" class="text-center">Item Code</th>
				<th width="150px" class="text-center">Part No./Part Name</th>
				<th width="200px" class="text-center">Size/Spec.</th>
				<th width="100px" class="text-center">Qty</th>
				<th class="text-center">Remarks</th>
			</tr>
			</thead>';
	

			$mpdf->SetHTMLHeader($sethdr);
			$mpdf->SetHTMLFooter($setfooter);

			if($xccnt>1){
				$mpdf->AddPageByArray(array(
					'resetpagenum' => '1'
				));
			}

			$sqldetx = mysqli_query($con,"Select A.*, B.citemdesc, B.cnotes from invtransfer_t A left join items B on A.compcode=B.compcode and A.citemno=B.cpartno where A.compcode='$company' and A.ctranno='".$row['ctranno']."' Order By A.nidentity");
			if (mysqli_num_rows($sqldetx)!=0) {
				$PRDet = array();
				$cnt = 0;
				while($rowxx = mysqli_fetch_array($sqldetx, MYSQLI_ASSOC)){
					$PRDet[] = $rowxx;
				}
			}


			$cnt = 0;
			if(count($PRDet)>0){
				foreach($PRDet as $rowdtls){
					$cnt++;

					$xcqty = (floor( $rowdtls['nqty'.$_REQUEST['n']] ) != $rowdtls['nqty'.$_REQUEST['n']]) ? number_format($rowdtls['nqty'.$_REQUEST['n']],2) : number_format($rowdtls['nqty'.$_REQUEST['n']]);

					$html = $html.'<tr>
						<td align="center" class="tdpadx tddetz">'.$cnt.'</td>
						<td align="center" class="tdpadx tddetz">'.$rowdtls['citemno'].'</td>
						<td align="center" class="tdpadx tddetz">'.$rowdtls['citemdesc'].'</td>
						<td align="center" class="tdpadx tddetz">'.$rowdtls['cnotes'].'</td>
						<td align="center" class="tdpadx tddetz">'.$xcqty." ".$rowdtls['cunit'].'</td>
						<td align="center" class="tdpadx tddetz">'.$rowdtls['cremarks'].'</td>		
					</tr>';

				} 
			}

			$html = $html.'</table>';

			//echo $sethdr;

			
			$mpdf->WriteHTML($html);

			//echo $sethdr.$html.$setfooter."<br><br>";

			}
		}

	$mpdf->Output($cno,'I');


?>