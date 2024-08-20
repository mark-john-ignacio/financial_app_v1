<?php
	if(!isset($_SESSION)){
		session_start();
	}

	include('../../vendor/autoload.php');

	// Set a simple Footer including the page number
	//$mpdf->setFooter('{PAGENO}');

	//ob_start();

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

	$csalesno = $_REQUEST['printid'];

	$sqlhead = mysqli_query($con,"select a.*, b.cdesc as locname, c.Fname, c.Minit, c.Lname, IFNULL(c.cusersign,'') as cusersign, d.cdesc as creqname from purchrequest a left join locations b on a.compcode=b.compcode and a.locations_id=b.nid left join users c on a.cpreparedby=c.Userid left join mrp_operators d on a.compcode=d.compcode and a.crequestedby=d.nid where a.compcode='$company' and a.ctranno = '$csalesno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$SecDesc = $row['locname'];

			$Remarks = $row['cremarks'];
			$Date = $row['ddate'];
			$DateNeeded = $row['dneeded'];
			
			$lCancelled = $row['lcancelled'];
			$lPosted = $row['lapproved'];
			$lSent = $row['lsent'];

			$cApprvBy = $row['capprovedby'];
			$cCheckedBy = $row['ccheckedby'];

			$cReqBy = $row['creqname'] ;

			$cpreparedBy = $row['Fname']." ".$row['Minit'].(($row['Minit']!=="" && $row['Minit']!==null) ? " " : "").$row['Lname'];
			$cpreparedBySign = $row['cusersign'];
		}
	}

	$sqlbody = mysqli_query($con,"select a.*, IFNULL(c.cdesc,'') as locdesc from purchrequest_t a left join items b on a.compcode=b.compcode and a.citemno=b.cpartno left join locations c on a.compcode=c.compcode and a.location_id=c.nid where a.compcode='$company' and a.ctranno = '$csalesno' Order by a.nident");
	$PRDet = array();
	if (mysqli_num_rows($sqlbody)!=0) {
		while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
			$PRDet[] = $rowdtls;
		}
	}
	

	$sethdr = '<table border="0" cellpadding="5px" width="100%" id="tblMain" style="border-collapse:collapse">
		<tr>
			<td height="50px" width="100px"> <img src="../'.$logosrc.'" width="100px"/> </td>
			<td style="text-align: center" height="50px"> <font style="font-size: 18px;">PURCHASE REQUISITION SLIP</font> </td>
			<td height="50px" width="100px" style="text-align: right"> {PAGENO} / {nbpg} </td>
		</tr>
		<tr>
			<td style="vertical-align: top; padding-top: 10px; padding-right: 5px;" colspan="3">

				<table border="0" width="100%" style="border-collapse:collapse">
					<tr>
						<td style="padding-bottom: 10px; padding-top: 10px">
							<font style="font-size: 14px;"><b>Department:</b> '.$SecDesc.'</font>
						</td>
						<td align="right" style="padding-bottom: 10px; padding-top: 10px">
							<font style="font-size: 14px;"><b>Date prepared:</b> '.date("F d, Y").'</font>
						</td>						
					</tr>
					<tr>
						<td style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b>Date needed:</b> '.date_format(date_create($DateNeeded),"F d, Y").'</font>
						</td>
						<td align="right" style="padding-bottom: 10px">
							<font style="font-size: 14px;"><b> PR No.:</b> '.$csalesno.'</font>
						</td>

					</tr>
				</table>
				
			</td>
		</tr>
	</table>';
	
	$setfooter = '<table border="1" width="100%" style="border-collapse:collapse">
	<tr>
		<td align="center" width="33%">
			<b>Requested By</b>
		</td>
		<td align="center" width="33%">
			<b>Checked By</b>
		</td>
		<td align="center">
			<b>Approved By</b>
		</td>
	</tr>
	<tr>
		<td align="center"  valign="top">
			<div style="text-align: center; display: block; height: 40px">&nbsp;</div>
			<div style="text-align: center; display: block\">'.$cReqBy.'</div>
		</td>
		<td align="center" valign="top">
			<div style="text-align: center; display: block; height: 40px">&nbsp;</div>
			<div style="text-align: center; display: block\">'.$cCheckedBy.'</div>												
		</td>
		<td align="center" valign="top">
			<div style="text-align: center; display: block; height: 40px">&nbsp;</div>
			<div style="text-align: center; display: block\">'.$cApprvBy.'</div>												
		</td>
	</tr>
</table><table border="0" width="100%" style="border-collapse:collapse; font-size: 10px">
<tr><td> '.date("h:i:sa") . ' '. date("d-m-Y").' </td><td> Note: In Case of Error Please Report  to the concern Department within 24hrs ERASURE IS NOT ALLOWED </td><td> BMRC-PC-001-F </td></tr></table>';
	//end foot

	$html = '<table border="1" align="center" width="100%" style="border-collapse: collapse;">
	<thead>
	<tr>
		<th class="tdpadx">No.</th>
		<th class="tdpadx">Part No.</th>
		<th class="tdpadx">Description/Size</th>
		<th class="tdpadx">Item Code</th>
		<th class="tdpadx">Qty</th>
		<th class="tdpadx">Unit</th>
		<th class="tdpadx">Remarks</th>
	</tr>
	</thead>
	';
	
	$cnt = 0;
	if(count($PRDet)>0){
		foreach($PRDet as $rowdtls){
			$cnt++;

			$html = $html.'<tr>
				<td align="center" class="tdpadx tddetz">'.$cnt.'</td>
				<td align="center" class="tdpadx tddetz">'.$rowdtls['cpartdesc'].'</td>
				<td align="center" class="tdpadx tddetz">'.$rowdtls['citemdesc'].'</td>
				<td align="center" class="tdpadx tddetz">'.$rowdtls['citemno'].'</td>
				<td align="center" class="tdpadx tddetz">'.intval($rowdtls['nqty']).'</td>
				<td align="center" class="tdpadx tddetz">'.$rowdtls['cunit'].'</td>					
				<td align="center" class="tdpadx tddetz">'.$rowdtls['cremarks']. (($rowdtls['cremarks']!="" && $rowdtls['locdesc']!="") ? "<br>" : ""). (($rowdtls['locdesc']!="") ? $rowdtls['locdesc'] : "").'</td>			
			</tr>';

		} 
	}

	$html = $html.'</table>';

	$mpdf = new \Mpdf\Mpdf([
		'mode' => '',
		'format' => [215.9, 139.7],
		'default_font_size' => 10,
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

	$mpdf->SetHTMLHeader($sethdr);
	$mpdf->SetHTMLFooter($setfooter);

	// send the captured HTML from the output buffer to the mPDF class for processing
	$mpdf->WriteHTML($html);
	$mpdf->Output($csalesno,'I');


?>