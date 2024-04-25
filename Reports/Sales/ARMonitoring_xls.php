<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";


	//use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use \PhpOffice\PhpSpreadsheet\Cell\DataType;

	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();


	$company = $_SESSION['companyid'];
	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}


	// Set document properties
	$spreadsheet->getProperties()->setCreator('Myx Financials')
		->setLastModifiedBy('Myx Financials')
		->setTitle('AR Monitoring')
		->setSubject('AR Monitoring Report')
		->setDescription('AR Monitoring Report, generated using Myx Financials.')
		->setKeywords('myx_financials ar_monitoring')
		->setCategory('Myx Financials Report');

	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', strtoupper($compname))
		->setCellValue('A2', 'AR MONITORING')
		->setCellValue('A3', 'For the Period '.date_format(date_create($_POST["date1"]),"F d, Y")." to ".date_format(date_create($_POST["date2"]),"F d, Y"));

	$spreadsheet->getActiveSheet()->mergeCells("A1:O1");
	$spreadsheet->getActiveSheet()->mergeCells("A2:O2");
	$spreadsheet->getActiveSheet()->mergeCells("A3:O3");

	// Add some data
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A5', 'Type')
		->setCellValue('B5', 'Transaction No.')
		->setCellValue('C5', 'Reference')
		->setCellValue('D5', 'Date')
		->setCellValue('E5', 'Customer')
		->setCellValue('F5', '')
		->setCellValue('G5', 'Vatable Sales')
		->setCellValue('H5', 'VAT%')
		->setCellValue('I5', 'VAT Amount')
		->setCellValue('J5', 'Sales Amount')
		->setCellValue('K5', 'EWT')
		->setCellValue('L5', 'EWT Amount')
		->setCellValue('M5', 'Adjustments')
		->setCellValue('N5', 'AR Balance Net of TAX')
		->setCellValue('O5', 'Amount Collected')
		->setCellValue('P5', 'Balance')
		->setCellValue('Q5', 'Status');

	$spreadsheet->getActiveSheet()->mergeCells("E5:F5");
	$spreadsheet->getActiveSheet()->getStyle('A5:Q5')->getFont()->setBold(true);

	$postedtran = $_POST["selrpt"];

	$mainqry = "";
	$finarray = array();

	$qryposted = "";
	if($postedtran==1 || $postedtran==0){
		$qryposted = " and B.lcancelled=0 and B.lvoid=0 and B.lapproved=".$postedtran."";
	}elseif($postedtran==2){
		$qryposted = " and (B.lcancelled=1 or B.lvoid=1)";
	}

	$transrefDR = array();
	$result=mysqli_query($con,"Select ctranno, GROUP_CONCAT(DISTINCT creference) as cref from sales_t where compcode='$company' group by ctranno");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$transrefDR[$row['ctranno']] = $row['cref'];
	}

	@$arrpaymnts = array();
	$sqlpay = "select X.* from receipt_sales_t X left join receipt B on X.compcode=B.compcode and X.ctranno=B.ctranno where X.compcode='$company' and B.lcancelled = 0 and B.lvoid=0 order By X.csalesno, B.ddate";
	$respay = mysqli_query ($con, $sqlpay);
	while($rowardj = mysqli_fetch_array($respay, MYSQLI_ASSOC)){
		@$arrpaymnts[] = $rowardj;
	}

	$transctions = array();
	$sqlx = "Select A.type, A.ctranno, A.ccode, A.cname, A.cacctid, A.cacctdesc, IFNULL(A.ctaxcode,'') as ctaxcode, A.nrate, IFNULL(A.cewtcode,'') as cewtcode, A.newtrate, A.dcutdate, SUM(ROUND(A.namountfull,2)) as ngross, SUM(ROUND(A.namount,2)) as cm, SUM(nvatgross) as nvatgross, (SUM(ROUND(A.namountfull,2)) - SUM(ROUND(A.namount,2)) - SUM(nvatgross)) as vatamt, A.lcancelled, A.lvoid, A.lapproved
	From (
		Select 'SI' as type, A.ctranno, B.ccode, COALESCE(C.ctradename, C.cname) as cname, A.citemno, ((A.nqtyreturned) * (A.nprice-A.ndiscount)) as namount, (A.nqty * (A.nprice-A.ndiscount)) as namountfull, B.dcutdate, D.cacctid, D.cacctdesc, A.ctaxcode, A.nrate, B.cewtcode, IFNULL(E.nrate,0) as newtrate, 
			CASE 
				WHEN IFNULL(A.nrate,0) <> 0 
				THEN 
					ROUND(((A.nqty-A.nqtyreturned)*(A.nprice-A.ndiscount))/(1 + (A.nrate/100)),2)
				ELSE 
					A.namount 
				END as nvatgross, B.lcancelled, B.lvoid, B.lapproved
	From sales_t A 
	left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno 
	left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
	left join accounts D on C.compcode=D.compcode and C.cacctcodesales=D.cacctno 
	left join wtaxcodes E on A.compcode=E.compcode and A.cewtcode=E.ctaxcode 
	where A.compcode='$company' and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$qryposted."
	
	UNION ALL

	Select 'BS' as type, A.ctranno, B.ccode, COALESCE(C.ctradename, C.cname) as cname, '' as citemno, 0 as namount, A.namount as namountfull, B.dcutdate, '' as cacctid, '' as cacctdesc, CASE WHEN B.cvattype='VatIn' THEN F.ctaxcode ELSE '' END as ctaxcode, CASE WHEN B.cvattype='VatIn' THEN F.nrate ELSE '' END as nrate, '' as cewtcode, 0 as newtrate, 	
		CASE 
			WHEN B.cvattype='VatIn'
			THEN 
				ROUND((A.nqty*A.nprice)/(1 + (F.nrate/100)),2)
			ELSE 
				A.namount 
			END as nvatgross, B.lcancelled, B.lvoid, B.lapproved
	From quote_t A
	left join quote B on A.compcode=B.compcode and A.ctranno=B.ctranno
	left join customers C on B.compcode=C.compcode and B.ccode=C.cempid 
	left join items E on A.compcode=E.compcode and A.citemno=E.cpartno 
	left join taxcode F on E.compcode=F.compcode and E.ctaxcode=F.ctaxcode
	left join (
		Select Y.creference From sales_t Y left join sales X on Y.compcode=X.compcode and Y.ctranno=X.ctranno 
		where Y.compcode='$company' and X.lcancelled=0 and X.lvoid=0
	) G on A.ctranno=G.creference
	where A.compcode='$company' and B.quotetype='billing' and B.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$qryposted." and IFNULL(G.creference,'') = ''

	) A
	Group By A.ctranno, A.ccode, A.cname, A.cacctid, A.cacctdesc, A.ctaxcode, A.nrate, A.cewtcode, A.newtrate, A.dcutdate, A.lcancelled, A.lvoid, A.lapproved
	order by A.dcutdate, A.ctranno";

	//echo $sqlx;

	$result=mysqli_query($con,$sqlx);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
		$transctions[] = $row['ctranno'];
	}

	$cnt = 5;
	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;

	$ARBal = 0;
	$CollBal = 0;
	$BalBal = 0;

	foreach($finarray as $row)
	{
		$invval = 
		$remarks = 
		$ccode =
		$xtypx =  
		$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
		$classcode="class='rpthead'";

		$ewtcode = 0;
		$vx = explode(";",$row['newtrate']);
		foreach($vx as $vx2){
			if($vx2!=""){
				$ewtcode = $ewtcode + floatval($vx2);
			}
		}
		
		$xccccref = "";
		if($row['type']=="SI") {
			$xccccref = $transrefDR[$row['ctranno']];
		}

		$xccccrefrate = "";
		if(intval($row['nrate'])!=0 && intval($row['nrate'])!=""){
			if(floatval($row['vatamt'])!=0) {
				$xccccrefrate = $row['vatamt'];
			}
		}

		$phpewtamt = 0;
		if(intval($ewtcode)!=0 && intval($ewtcode)!=""){
			$phpewtamt = floatval($row['nvatgross']) * (floatval($ewtcode)/100);
		}

		$npay = 0;
		$cntofist = 0;
		$nadjs = 0;
		$nadjsdm = 0;
		$nadjscm = 0;

		foreach(@$arrpaymnts as $rxpymnts){
			if($row['ctranno']==$rxpymnts['csalesno']){
				$cntofist++;
				
				if($cntofist==1){
					$ntotal = floatval($rxpymnts['ndue']) - floatval($rxpymnts['napplied']);
				}

				$npay = $npay + floatval($rxpymnts['napplied']);

				if($rxpymnts['ndm']>1){
					$nadjsdm = $nadjsdm + floatval($rxpymnts['ndm']);
				}

				if($rxpymnts['ncm']>1){
					$nadjscm = $nadjscm + floatval($rxpymnts['ncm']);
				}
			}
		}

		$nadjs = ($nadjs + $nadjsdm) - $nadjscm;

		$netvatamt = floatval($row['ngross']) - floatval($phpewtamt) + $nadjs;
		$nbalace = floatval($netvatamt) - floatval($npay);

		if($row['lcancelled']==1 || $row['lvoid']==1){
			if($row['lcancelled']==1){
				$xycolor = "Cancelled";
			}

			if($row['lvoid']==1){
				$xycolor = "Void";
			}
			
		}else{
			if($row['lapproved']==1){
				$xycolor = "Posted";
			}else{
				$xycolor = "Pending";
			}
		}

		$ARBal += floatval($netvatamt);
		$CollBal += floatval($npay);
		$BalBal += floatval($nbalace);

		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cnt, $row['type'])
		->setCellValue('B'.$cnt, $row['ctranno'])
		->setCellValue('C'.$cnt, $xccccref)
		->setCellValue('D'.$cnt, $dateval)
		->setCellValue('E'.$cnt, $row['ccode'])
		->setCellValue('F'.$cnt, $row['cname'])
		->setCellValue('G'.$cnt, (floatval($row['nvatgross'])!=0) ? $row['nvatgross'] : "")
		->setCellValue('H'.$cnt, (intval($row['nrate'])!=0 && intval($row['nrate'])!="") ? number_format($row['nrate'])."%" : "")
		->setCellValue('I'.$cnt, $xccccrefrate)
		->setCellValue('J'.$cnt, (floatval($row['ngross'])!=0) ? $row['ngross'] : "")
		->setCellValue('K'.$cnt, (intval($ewtcode)!=0 && intval($ewtcode)!="") ? number_format($ewtcode)."%" : "")
		->setCellValue('L'.$cnt, (floatval($phpewtamt)!=0) ? $phpewtamt : "")
		->setCellValue('M'.$cnt, $nadjs)
		->setCellValue('N'.$cnt, $netvatamt)
		->setCellValue('O'.$cnt, (floatval($npay)!=0) ? $npay : "")
		->setCellValue('P'.$cnt, $nbalace)
		->setCellValue('Q'.$cnt, $xycolor);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('O'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('P'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('Q'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	}

	$cnt++;
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cnt, "GRAND TOTAL")
		->setCellValue('N'.$cnt, $ARBal)
		->setCellValue('O'.$cnt, $CollBal)
		->setCellValue('P'.$cnt, $BalBal);

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":M".$cnt);
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":P".$cnt)->getFont()->setBold(true);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('N'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('O'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('P'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('ARMonitoring');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ARMonitoring.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

?>