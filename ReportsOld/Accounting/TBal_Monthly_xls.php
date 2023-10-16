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
	
		$hdr_months = array_unique($months);
		asort($hdr_months);
	
		$hdr_accts = array_unique($qry_accts);
		asort($hdr_accts);

		// HEADER //
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Account No.');
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Account Name');

		$GtotDr = array();
		$GtotCr = array();

		$cols = 2;

			foreach($hdr_months as $rx){
				$monthNum  = $rx;
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F');

				$GtotDr[$rx] = 0;
				$GtotCr[$rx] = 0;

				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $monthName." Dr.");
				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $monthName." Cr.");
				$cols++;
				$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $monthName." Balance");

			}

		// END HEADER //

		// BODY //

		$ntotdebit = 0;
		$ntotcredit = 0;
		$cntr=0;
		$ntotbal = 0;
		$ntotGBal = 0;

		$cols = 0;
		$rows = 1;
		foreach($hdr_accts as $rx)
		{
			$rows++;

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rx);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $qry_acctsnames[$rx]);


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
		// END FOOTER //

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