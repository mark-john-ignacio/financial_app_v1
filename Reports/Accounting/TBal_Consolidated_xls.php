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

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Myx Financials')
		->setLastModifiedBy('Myx Financials')
		->setTitle('Trial Balance Report')
		->setSubject('Trial Balance Report')
		->setDescription('Trial Balance Report, generated using Myx Financials.')
		->setKeywords('myx_financials trial_balance')
		->setCategory('Myx Financials Report');

	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Account No.')
    ->setCellValue('B1', 'Account Title')
		->setCellValue('C1', 'Debit')
		->setCellValue('D1', 'Credit')
		->setCellValue('E1', 'Balance');

	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);


	$company = $_SESSION['companyid'];
	$arrcomps = array();
	$arrcompsname = array();
	$arrcompsids = array();
	$sql = "select compcode, compname From company";
	$result=mysqli_query($con,$sql);
  	$companycnt=mysqli_num_rows($result);
	if($companycnt>0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrcomps[] = $row;
			$arrcompsname[] = $row['compname'];
			$arrcompsids[] = $row['compcode'];
		}
	}

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$qrytotdebit = array();
	$qrytotcredit = array();
	$qry_accts = array();
	$qry_acctsnames = array();

	$sql = "Select A.compcode, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode in ('".implode("','", $arrcompsids)."') and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
			Group By A.compcode, A.acctno, B.cacctdesc
			Order By A.compcode, A.acctno";

		//	echo $sql;

	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$qry_accts[] = $row['acctno'];
		$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];

		$qrytotdebit[$row['compcode']][$row['acctno']] = $row['ndebit'];
		$qrytotcredit[$row['compcode']][$row['acctno']] = $row['ncredit'];
	}

	$hdr_accts = array_unique($qry_accts);
	asort($hdr_accts);

	// HEADER //
		$totdrcrcnt = $companycnt*3;
		$mergedcols = (($companycnt + 1) * 3) + 2;

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 1)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A1:".$xcol2);

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 2)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A2:".$xcol2);

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 3)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A3:".$xcol2);

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A1', 'Company: '.implode(", ",$arrcompsname))
			->setCellValue('A2', 'Trial Balance - (Consolidated)')
			->setCellValue('A3', 'For the Year'.$dteyr);
	//Top header
	//Table Header
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Account No.');
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Account Name');
		$spreadsheet->getActiveSheet()->mergeCells("A5:A6");
		$spreadsheet->getActiveSheet()->mergeCells("B5:B6");
		$spreadsheet->getActiveSheet()->getStyle("A5:B6")->getAlignment()->setVertical('center');


		$ntotdebit = array();
		$ntotcredit = array();

		$cols=3;
		foreach($arrcomps as $row){

			$ntotdebit[$row['compcode']] = 0;
			$ntotcredit[$row['compcode']] = 0;

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, $row['compname']);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 5)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

			$cols+=3;
		}

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, 'Grand Total');
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 5)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

		
		$cols = 2;
		foreach($arrcomps as $row){
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Debit");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Credit");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Balance");
		}
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Debit");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Credit");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Balance");

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 6)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A1:".$xcol2)->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle("A5:".$xcol2)->getAlignment()->setHorizontal('center');
	//End Table Header

	//BODY
		$ntotbal = 0;
		$ntotGBal = 0;
		
		$xdrrowtot = 0;
		$xcrrowtot = 0;
		$xtotalrow = 0;

		$Gxdrrowtot = 0;
		$Gxcrrowtot = 0;
		$Gxtotalrow = 0;

		$cols = 0;
		$rows = 6;	
		foreach($hdr_accts as $rz){
			$rows++;

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rz);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $qry_acctsnames[$rz]);


			foreach($arrcomps as $row){

				if(isset($qrytotdebit[$row['compcode']][$rz])){
					$ndramt = $qrytotdebit[$row['compcode']][$rz];
				}else{
					$ndramt = 0;
				}
	
				if(isset($qrytotcredit[$row['compcode']][$rz])){
					$ncramt = $qrytotcredit[$row['compcode']][$rz];
				}else{
					$ncramt = 0;
				}
	
				$ntotdebit[$row['compcode']] = $ntotdebit[$row['compcode']] + floatval($ndramt);
				$ntotcredit[$row['compcode']] = $ntotcredit[$row['compcode']] + floatval($ncramt);
	
				$ntotbal = floatval($ndramt) - floatval($ncramt);
	
				$xdrrowtot += floatval($ndramt);
				$xcrrowtot += floatval($ncramt);

				$cols++;				
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $ndramt);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $ncramt);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $ntotbal);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			}

			$xtotalrow = floatval($xdrrowtot) - floatval($xcrrowtot);

			$cols++;				
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xdrrowtot);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xcrrowtot);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xtotalrow);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$Gxdrrowtot += floatval($xdrrowtot);
			$Gxcrrowtot += floatval($xcrrowtot);

			$xdrrowtot = 0;
			$xcrrowtot = 0;
			$xtotalrow = 0;

			$cols = 0;
		}
	//END BODY

	//FOOTER TOTAL
		$cols = 0;
		$rows++;
		
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL");
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

		foreach($arrcomps as $row){
			$ntotGBal = floatval($ntotdebit[$row['compcode']]) - floatval($ntotcredit[$row['compcode']]);

			$cols++;				
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $ntotdebit[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $ntotcredit[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$ntotGBal);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}

		$Gxtotalrow = floatval($Gxdrrowtot) - floatval($Gxcrrowtot);

		$cols++;				
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $Gxdrrowtot);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$cols++;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $Gxcrrowtot);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$cols++;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $Gxtotalrow);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	//END FOOTER TOTAL

	$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);
	$sheet = $spreadsheet->getActiveSheet();
	foreach ($sheet->getColumnIterator() as $column) {
		$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
	}

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Trial_Balance');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Trial_Balance.xlsx"');
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