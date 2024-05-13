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

	$arrcomps = array();
	$arrcompsname = array();
	
	$sql = "select compcode, compname From company";
	$result=mysqli_query($con,$sql);
  	$companycnt=mysqli_num_rows($result);
	if($companycnt>0){
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrcomps[] = $row;
			$arrcompsname[] = $row['compname'];
		}
	}

		$sql = "Select A.compcode, MONTH(ddate) as dmonth, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where YEAR(A.ddate) = '$dteyr' and IFNULL(B.cacctdesc,'') <> ''
		Group By A.compcode, MONTH(ddate), A.acctno, B.cacctdesc
		Order By A.compcode, A.acctno, MONTH(ddate)";
	
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


			$qrytotdebit[$row['compcode']][$row['dmonth']][$row['acctno']] = $row['ndebit'];
			$qrytotcredit[$row['compcode']][$row['dmonth']][$row['acctno']] = $row['ncredit'];

			$qryrows[] = $row;
		}

	//For Beg Bal
		$dteyrminus = $dteyr - 1;
		$begtotdebit = array();
		$begtotcredit = array();

		$sql = "Select A.compcode, A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
		From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
		where YEAR(A.ddate) = '$dteyrminus' and IFNULL(B.cacctdesc,'') <> ''
		Group By A.compcode, A.acctno, B.cacctdesc
		Order By A.compcode, A.acctno";

		$result=mysqli_query($con,$sql);
		$qryrows = array();
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if(!in_array($row['acctno'], $qry_accts)){
				$qry_accts[] = $row['acctno'];
				$qry_acctsnames[$row['acctno']] = $row['cacctdesc'];
			}

			$begtotdebit[$row['compcode']][$row['acctno']] = $row['ndebit'];
			$begtotcredit[$row['compcode']][$row['acctno']] = $row['ncredit'];

		}
	//End BegBal	
	
		$hdr_months = array_unique($months);
		asort($hdr_months);
	
		$hdr_accts = array_unique($qry_accts);
		asort($hdr_accts);

	// HEADER //
		$totdrcrcnt = $companycnt*3;
		$mergedcols = ((count($hdr_months) + 2) * $totdrcrcnt) + 2;

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 1)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A1:".$xcol2);

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 2)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A2:".$xcol2);

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($mergedcols, 3)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells("A3:".$xcol2);

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A1', 'Company: '.implode(", ",$arrcompsname))
			->setCellValue('A2', 'Trial Balance - Monthly (Consolidated)')
			->setCellValue('A3', 'For the Year'.$dteyr);

		$cols = 2;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 5, 'Account No.');
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Account Name');

		$spreadsheet->getActiveSheet()->mergeCells("A5:A7");
		$spreadsheet->getActiveSheet()->mergeCells("B5:B7");

		$spreadsheet->getActiveSheet()->getStyle("A5:B7")->getAlignment()->setVertical('center');

		$totstar = $totdrcrcnt-1;
		$cols++;	
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, 'Beginning Balance ('.$dteyrminus.')');
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+$totstar, 5)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

		foreach($hdr_months as $rx){
			$monthNum  = $rx;
			$dateObj   = DateTime::createFromFormat('!m', $monthNum);
			$monthName = $dateObj->format('F');

			$cols += $totdrcrcnt;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, $monthName);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+$totstar, 5)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

		}

		$cols += $totdrcrcnt;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 5, 'Grand Total');
		$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 5)->getCoordinate();
		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+$totstar, 5)->getCoordinate();
		$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);


		$cols = 3;
		//Company Beg bal
		foreach($arrcomps as $row){
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, $row['compname']);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 6)->getCoordinate();
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 6)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

			$cols+=3;
		}
		//End
		//company per month
			foreach($hdr_months as $rx){
				foreach($arrcomps as $row){
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, $row['compname']);
					$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 6)->getCoordinate();
					$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 6)->getCoordinate();
					$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);
					$cols+=3;
				}
			}
		//end
		//company grand total
		foreach($arrcomps as $row){
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 6, $row['compname']);
			$xcol1 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 6)->getCoordinate();
			$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, 6)->getCoordinate();
			$spreadsheet->getActiveSheet()->mergeCells($xcol1.":".$xcol2);

			$cols+=3;
		}
		//End


		$cols = 2;
		foreach($arrcomps as $row){
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Debit");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Credit");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Balance");
		}

		$GtotDr = array();
		$GtotCr = array();

			foreach($hdr_months as $rx){
				foreach($arrcomps as $row){
					$GtotDr[$row['compcode']][$rx] = 0;
					$GtotCr[$row['compcode']][$rx] = 0;

					$cols++;
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Debit");
					$cols++;
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Credit");
					$cols++;
					$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Balance");
				}
			}

		foreach($arrcomps as $row){//for garandtot
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Debit");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Credit");
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 7, "Balance");
		}

		$xcol2 = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, 7)->getCoordinate();
		$spreadsheet->getActiveSheet()->getStyle("A1:".$xcol2)->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle("A5:".$xcol2)->getAlignment()->setHorizontal('center');

	// END HEADER //

	// BODY //

		$ntotdebit = 0;
		$ntotcredit = 0;
		$cntr=0;
		$ntotbal = 0;
		$ntotGBal = 0;
		
		$GtotDrBeg = array();
		$GtotCrBeg = array();

		$rowdebit = array();
		$rowcredit = array();
		$nrowttal = 0;

		$GRowDr = array();
		$GRowCr = array();
		$GRowCrTot = 0;

		foreach($arrcomps as $row){
			$GtotDrBeg[$row['compcode']] = 0;
			$GtotCrBeg[$row['compcode']] = 0;

			$rowdebit[$row['compcode']] = 0;
			$rowcredit[$row['compcode']] = 0;

			$GRowDr[$row['compcode']] = 0;
			$GRowCr[$row['compcode']] = 0;
		}

		$cols = 0;
		$rows = 7;	

		foreach($hdr_accts as $rx)
		{
			$rows++;

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rx);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $qry_acctsnames[$rx]);


			//For beginning
			foreach($arrcomps as $row){
				if(isset($begtotdebit[$row['compcode']][$rx])){
					$ndramt = $begtotdebit[$row['compcode']][$rx];
				}else{
					$ndramt = 0;
				}

				if(isset($begtotcredit[$row['compcode']][$rx])){
					$ncramt = $begtotcredit[$row['compcode']][$rx];
				}else{
					$ncramt = 0;
				}

				$ntotbal = floatval($ndramt) - floatval($ncramt);
				$GtotDrBeg[$row['compcode']] = $GtotDrBeg[$row['compcode']] + floatval($ndramt);
				$GtotCrBeg[$row['compcode']] = $GtotCrBeg[$row['compcode']] + floatval($ncramt);

				$rowdebit[$row['compcode']] = $rowdebit[$row['compcode']] + floatval($ndramt);
				$rowcredit[$row['compcode']] = $rowcredit[$row['compcode']] + floatval($ncramt);

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
			//end for beginning


			$cnts = 0;
			$ndramt = 0;
			$ncramt = 0;
			foreach($hdr_months as $rz){
				$cnts++;

				foreach($arrcomps as $row){
					if(isset($qrytotdebit[$row['compcode']][$rz][$rx])){
						$ndramt = $qrytotdebit[$row['compcode']][$rz][$rx];
					}else{
						$ndramt = 0;
					}

					if(isset($qrytotcredit[$row['compcode']][$rz][$rx])){
						$ncramt = $qrytotcredit[$row['compcode']][$rz][$rx];
					}else{
						$ncramt = 0;
					}

					$ntotbal = floatval($ndramt) - floatval($ncramt);

					$GtotDr[$row['compcode']][$rz] = $GtotDr[$row['compcode']][$rz] + floatval($ndramt);
					$GtotCr[$row['compcode']][$rz] = $GtotCr[$row['compcode']][$rz] + floatval($ncramt);

					$rowdebit[$row['compcode']] = $rowdebit[$row['compcode']] + floatval($ndramt);
					$rowcredit[$row['compcode']] = $rowcredit[$row['compcode']] + floatval($ncramt);

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
			}

			foreach($arrcomps as $row){

				$GRowDr[$row['compcode']] =  $GRowDr[$row['compcode']] + floatval($rowdebit[$row['compcode']]);
				$GRowCr[$row['compcode']] = $GRowCr[$row['compcode']] + floatval($rowcredit[$row['compcode']]);

				$nrowttal = floatval($rowdebit[$row['compcode']]) - floatval($rowcredit[$row['compcode']]);

				$cols++;				
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rowdebit[$row['compcode']]);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rowcredit[$row['compcode']]);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nrowttal);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$rowdebit[$row['compcode']] = 0;
				$rowcredit[$row['compcode']] = 0; 
				$nrowttal = 0;
			}


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
		
		//TotalBeg
		foreach($arrcomps as $row){
			$cols++;				   
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotDrBeg[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotCrBeg[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$ntotGBalBeg = $GtotDrBeg[$row['compcode']] - $GtotCrBeg[$row['compcode']];
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$ntotGBalBeg[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}
		//End total Beg

		foreach($hdr_months as $rz){
			foreach($arrcomps as $row){
				$ntotGBal = 0;

				$GtotDr[$row['compcode']][$rz] = $GtotDr[$row['compcode']][$rz] + floatval($ndramt);
				$GtotCr[$row['compcode']][$rz] = $GtotCr[$row['compcode']][$rz] + floatval($ncramt);

				$ntotGBal = $GtotDr[$row['compcode']][$rz] - $GtotCr[$row['compcode']][$rz];
				
				$cols++;				
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GtotDr[$row['compcode']][$rz]);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $Gxcrrowtot);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

				$cols++;
				$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$ntotGBal);
				$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			}
		}

		//Grand Total
		foreach($arrcomps as $row){ 
			$cols++;				   
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GRowDr[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $GRowCr[$row['compcode']]);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cols++;
			$GRowCrTot = $GRowDr[$row['compcode']] - $GRowCr[$row['compcode']];
			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows,$GRowCrTot);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}
		//End Grand Total

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