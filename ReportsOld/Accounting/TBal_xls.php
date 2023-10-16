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
	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$sql = "Select A.acctno, B.cacctdesc, sum(A.ndebit) as ndebit, sum(A.ncredit) as ncredit
			From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			where A.compcode='$company' and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
			Group By A.acctno, B.cacctdesc
			Order By A.acctno";

		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$cnt = 1;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cnt++;

		$ntotdebit = $ntotdebit + floatval($row['ndebit']);
		$ntotcredit = $ntotcredit + floatval($row['ncredit']);

		$ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

		$ntotGBal = $ntotGBal + $ntotbal;

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['acctno'])
			->setCellValue('B'.$cnt, $row['cacctdesc'])
			->setCellValue('C'.$cnt, $row['ndebit'])
			->setCellValue('D'.$cnt, $row['ncredit'])
			->setCellValue('E'.$cnt, $ntotbal);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}
	$cnt += 2;
    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('C'.$cnt, 'Total')
            ->setCellValue('D'.$cnt, floatval($ntotdebit))
            ->setCellValue('E'.$cnt, floatval($ntotcredit));

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