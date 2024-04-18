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
	$sqlhead = mysqli_query($con,"SELECT a.*, b.*, c.Fname, c.Lname, c.Minit, IFNULL(c.cusersign,'') as cusersign, IFNULL(d.cusersign,'') as cchecksign FROM `receive` a
	left join `suppliers` b on a.compcode = b.compcode and a.ccode = b.ccode
	left join `users` c on a.cpreparedby = c.Userid
	left join `users` d on a.lappbyid = d.Userid
	where a.compcode = '$company' and a.ctranno = '$csalesno'");

	if (mysqli_num_rows($sqlhead)!=0) {
		while($row = mysqli_fetch_array($sqlhead, MYSQLI_ASSOC)){
			$SupName = $row['cname'];
            $address = $row['chouseno'] . " " . $row['ccity'] . " " . $row['cstate'] . " " . $row['ccountry'];
            $SupZip = $row['czip'];
            $Terms = $row['cterms'];
            $SupTin = $row['ctin'];
            $date = $row['dreceived'];

            $lposted = $row['lapproved'];

            $delto = $row['Fname'] . " " . $row['Lname'];
            $Remarks = $row['cremarks'];
            $Gross = $row['ngross'];
            $cpreparedBy = $row['Fname']." ".(($row['Minit']!=="" && $row['Minit']!==null) ? " " : $row['Minit']).$row['Lname'];
		    $cpreparedBySign = $row['cusersign'];

            $cCheckedBy = $row['lappby'];
		    $cCheckedBySign = $row['cchecksign'];
		}
	}

	$sqlbody = mysqli_query($con,"SELECT a.*, b.cpartno, b.citemdesc, b.cunit FROM receive_t a
	left join items b on a.compcode = b.compcode and b.cpartno = a.citemno
	where a.compcode = '$company' and a.ctranno = '$csalesno' Order by a.nident");
	$PRDet = array();
	if (mysqli_num_rows($sqlbody)!=0) {
		while($rowdtls = mysqli_fetch_array($sqlbody, MYSQLI_ASSOC)){ 
			$PRDet[] = $rowdtls;
		}
	}
	

	$sethdr = '<table border="0" cellpadding="0px" width="100%" id="tblMain" style="border-collapse:collapse">
		<tr>
			<td height="50px" width="100px"> <img src="../'.$logosrc.'" width="100px"/> </td>
			<td style="text-align: center" height="50px"><font style="font-size: 18px;">PURCHASED STOCK-IN SLIP<br>(RECEIVED FROM SUPPLIER/SOURCE)</font> </td>
			<td height="50px" width="100px" style="text-align: right"> {PAGENO} / {nbpg} </td>
		</tr>
		<tr>
			<td style="vertical-align: top; padding-top: 10px; padding-right: 5px;" colspan="3">

				<table border="0" width="100%" cellspacing="0">
					<tr>
						<td width="50%" style="border-left: 1px solid #000; border-right: 1px solid #000; border-top: 1px solid #000; padding: 5px">
							Supplier/Source: 
						</td>
						<td width="30%" style="border-top: 1px solid #000; padding: 5px">
							Reference No.:
						</td>
						<td style="border-left: 1px solid #000; border-top: 1px solid #000; border-right: 1px solid #000; padding: 5px" nowrap>
							Date: '.$date.'
						</td>
					</tr>
					<tr>
						<td width="50%" style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 5px">
							'.$SupName.'
						</td>
						<td width="30%" style="border-bottom: 1px solid #000; padding: 5px">
							&nbsp;
						</td>
						<td style="border-left: 1px solid #000; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px">
							PSS No.: '.$tranno.'
						</td>
					</tr>
				</table>
				
			</td>
		</tr>
	</table>';
	
	$setfooter = '<table border="1" width="100%" style="border-collapse:collapse">
	<tr>
		<td width="33%" style="border-right: 1px solid #000; padding: 5px">
			PSS Prepared By/Date:
		</td>
		<td width="34%" style="border-right: 1px solid #000; padding: 5px">
			Checked By: (Name/Sign/Dept/Date)
		</td>
		<td width="33%" style="padding: 5px">
			Acknowledged By: (Purchasing/Date)
		</td>
	</tr>
	<tr>
		<td align="center"  valign="top">';
		if($cpreparedBySign!=""){  
			$setfooter = $setfooter.'<div style="text-align: center"><div><img src = '.$cpreparedBySign.'?x='.time().' height="80px"></div> 
			<div style="text-align: center">'.$cpreparedBy.'</div>';
		}
		else{
			$setfooter = $setfooter.'<div style="text-align: center"><div style="height:80px">&nbsp;</div></div>
			<div style="text-align: center">'.$cpreparedBy.'</div>';
		}
			
		$setfooter = $setfooter.'</td>
		<td align="center" valign="top">';
		if($lposted==1 && $cCheckedBySign!=""){
			$setfooter = $setfooter.'<div style="text-align: center"><div><img src = '.$cCheckedBySign.'?x='.time().' height="80px"></div> 
			<div style="text-align: center">'.$cCheckedBy.'</div> ';
		}else{
			$setfooter = $setfooter.'<div style="text-align: center"><div style="height:80px">&nbsp;</div></div>
			<div style="text-align: center">'.$cCheckedBy.'</div>';
		}
											
		$setfooter = $setfooter.'</td>
		<td style="padding: 5px">&nbsp;</td>
	</tr>
</table><table border="0" width="100%" style="border-collapse:collapse; font-size: 10px">
<tr><td> '.date("h:i:sa") . ' '. date("d-m-Y").' </td><td> Note: In Case of Error Please Report  to the concern Department within 24hrs ERASURE IS NOT ALLOWED </td><td> BMRC-LW-021-G </td></tr></table>';
	//end foot

	$html = '<table border="1" align="center" width="100%" style="border-collapse: collapse; margin-top: 5px" cellpadding="5px">
	<thead>
	<tr>
		<th style="width: 5%;">No</th>
		<th>Code</th>
		<th>Name</th>
		<th>Qty</th>
		<th>Size/Spec</th>
		<th>Unit</th>
		<th>POR</th>
		<th>Cost Center</th>
		<th>Remarks</th>
	</tr>
	</thead>
	';
	
	$cnt = 0;
	if(count($PRDet)>0){
		foreach($PRDet as $rowdtls){
			$cnt++;
			// for items
			$itemcode = $rowdtls['cpartno'];
			$itemname = $rowdtls['citemdesc'];
			$itemunit = $rowdtls['cunit'];
	
			//for sales return details
			$qty = $rowdtls['nqty'];
			$price = $rowdtls['nprice'];
			$amount = $rowdtls['namount']; 

			$html = $html.'<tr>
            <td align="center">'.$cnt.'</td>
            <td align="center">'.$rowdtls['citemno'].'</td>
            <td align="center">'.$rowdtls['cskucode'].'</td>
            <td align="center">'.number_format($rowdtls['nqty']).'</td>
            <td align="center">'.$rowdtls['citemdesc'].'</td>
            <td align="center">'.$rowdtls['cunit'].'</td>
            <td align="center">'.$rowdtls['creference'].'</td>
            <td align="center">'.(($rowdtls['ncostcenterdesc']==null || $rowdtls['ncostcenterdesc']=="null") ? "" : $rowdtls['ncostcenterdesc']).'</td>
            <td align="center">'.$rowdtls['cremarks'].'</td>
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