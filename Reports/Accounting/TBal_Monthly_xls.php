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

	$company = $_SESSION['companyid'];
	$dteyr = $_POST["selyr"];

	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);			
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}


		$sql = "Select MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode='$company' and YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
			Group By MONTH(ddate), A.acctno, B.cacctdesc
			Order By A.acctno, MONTH(ddate)";
	
			//echo $sql;
	
		$result=mysqli_query($con,$sql);
		if (!mysqli_query($con, $sql)) {
			printf("Errormessage: %s\n", mysqli_error($con));
		} 
	
		$qry_accts = array();
		$qry_acctsnames = array();
		$months = array();
	
		$qrytotdebit = array();
		$qrytotcredit = array();
	
		$qryrows = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$qry_accts[] = $row['acctno'];
			$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];
			$months[] = $row['dmonth'];
	
	
			$qrytotdebit[$row['dmonth']][$row['acctno']] = $row['ndebit'];
			$qrytotcredit[$row['dmonth']][$row['acctno']] = $row['ncredit'];
	
			$qryrows[] = $row;
		}


		//For Beg Bal
			$dteyrminus = $dteyr - 1;
			$begtotdebit = array();
			$begtotcredit = array();

			$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode='$company' and YEAR(A.ddate) = '$dteyrminus' and IFNULL(B.cacctdesc,'') <> ''
			Group By A.acctno, B.cacctdesc
			Order By A.acctno";

			$result=mysqli_query($con,$sql);
			$qryrows = array();
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				if(!in_array($row['acctno'], $qry_accts)){
					$qry_accts[] = $row['acctno'];
					$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];
				}

				$begtotdebit[$row['acctno']] = $row['ndebit'];
				$begtotcredit[$row['acctno']] = $row['ncredit'];

			}
		// End Beg bal
		
		$hdr_months = array_unique($months);
		asort($hdr_months);
	
		$hdr_accts = array_unique($qry_accts);
		asort($hdr_accts);

	// HEADER //
		$mergedcols = ((count($hdr_months) + 1) * 3) + 5;

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 1)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A1:".$xcol2);

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 2)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A2:".$xcol2);

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 3)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A3:".$xcol2);

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A1', $compname)
			->setCellValue('A2', 'Trial Balance - Monthly')
			->setCellValue('A3', 'For the Year'.$dteyr);

		$cols = 2;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Account No.');
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Account Name');

		$spreadsheet->getActiveSheet()->mergeCells("A5:A6");
		$spreadsheet->getActiveSheet()->mergeCells("B5:B6");

		$spreadsheet->getActiveSheet()->getStyle("A5:B6")->getAlignment()->setVertical('center');

		$cols++;		
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, 'Beginning Balance ('.$dteyrminus.')');
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 5)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

		foreach($hdr_months as $rx){
			$monthNum  = $rx;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('F');

			$cols += 3;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, $monthName);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 5)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

		}

		$cols += 3;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, 'Grand Total');
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 5)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);


		$cols = 2;

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Debit");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Credit");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, "Balance");

		$GtotDr = array();
		$GtotCr = array();

			foreach($hdr_months as $rx){

				$GtotDr[$rx] = 0;
				$GtotCr[$rx] = 0;

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
		$spreadsheet->getActiveSheet()->getStyle("A1:".$xcol2)->getAlignment()->setHorizontal('center');

	// END HEADER //

	// BODY //

		$ntotdebit = 0;
		$ntotcredit = 0;
		$cntr=0;
		$ntotbal = 0;
		$ntotGBal = 0;
		$GtotDrBeg = 0;
		$GtotCrBeg = 0;

		$rowdebit = 0;
		$rowcredit = 0;
		$nrowttal = 0;

		$GRowDr = 0;
		$GRowCr = 0;
		$GRowCrTot = 0;

		$cols = 0;
		$rows = 6;	

		foreach($hdr_accts as $rx)
		{
			$rows++;

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rx);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $qry_acctsnames[$rx]);


			//For beginning
				if(isset($begtotdebit[$rx])){
					$ndramt = $begtotdebit[$rx];
				}else{
					$ndramt = 0;
				}

				if(isset($begtotcredit[$rx])){
					$ncramt = $begtotcredit[$rx];
				}else{
					$ncramt = 0;
				}

				$ntotbal = floatval($ndramt) - floatval($ncramt);
				$GtotDrBeg = $GtotDrBeg + floatval($ndramt);
				$GtotCrBeg = $GtotCrBeg + floatval($ncramt);

				$rowdebit = $rowdebit + $ndramt;
				$rowcredit = $rowcredit + $ncramt;

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
			//end for beginning


			$cnts = 0;
			$ndramt = 0;
			$ncramt = 0;
			foreach($hdr_months as $rz){
				$cnts++;

				if(isset($qrytotdebit[$rz][$rx])){
					$ndramt = $qrytotdebit[$rz][$rx];
				}else{
					$ndramt = 0;
				}

				if(isset($qrytotcredit[$rz][$rx])){
					$ncramt = $qrytotcredit[$rz][$rx];
				}else{
					$ncramt = 0;
				}

				$ntotbal = floatval($ndramt) - floatval($ncramt);

				$GtotDr[$rz] = $GtotDr[$rz] + floatval($ndramt);
				$GtotCr[$rz] = $GtotCr[$rz] + floatval($ncramt);

				$rowdebit = $rowdebit + $ndramt;
				$rowcredit = $rowcredit + $ncramt;

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

			//rowtotal
				$GRowDr =  $GRowDr + floatval($rowdebit);
				$GRowCr = $GRowCr + floatval($rowcredit);

				$nrowttal = floatval($rowdebit) - floatval($rowcredit);

				$cols++;				
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rowdebit);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rowcredit);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nrowttal);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$rowdebit = 0;
				$rowcredit = 0;
				$nrowttal = 0;


			$cols = 0;
		}

	// END BODY //


	// FOOTER //
		$cols = 0;
		$rows++;
		
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL");
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		
		$cols++;				   
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotDrBeg);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$cols++;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotCrBeg);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$cols++;
		$ntotGBalBeg = $GtotDrBeg - $GtotCrBeg;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$ntotGBalBeg);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		

		foreach($hdr_months as $rz){

			$ntotGBal = 0;

			$GtotDr[$rz] = $GtotDr[$rz] + floatval($ndramt);
			$GtotCr[$rz] = $GtotCr[$rz] + floatval($ncramt);

			$ntotGBal = $GtotDr[$rz] - $GtotCr[$rz];
			
			$cols++;				
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotDr[$rz]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotCr[$rz]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$ntotGBal);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		}

			$GRowCrTot = $GRowDr - $GRowCr;

			$cols++;				
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GRowDr);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GRowCr);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$GRowCrTot);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");


		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle($xcol1.":".$xcol2)->getFont()->setBold(true);
	// END FOOTER //

	$sheet = $spreadsheet->getActiveSheet();
	foreach ($sheet->getColumnIterator() as $column) {
		$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
	}

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Trial_Balance_Monthly');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Trial_Balance_Monthly.xlsx"');
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