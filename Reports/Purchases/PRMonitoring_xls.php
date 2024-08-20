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
		->setTitle('Purchase Detailed')
		->setSubject('Purchase Detailed Report')
		->setDescription('Purchase Report, generated using Myx Financials.')
		->setKeywords('myx_financials purchase_report')
		->setCategory('Myx Financials Report');


	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', $compname);

	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A2', 'Purchase Request Monitoring');

	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A3', 'For the Period '.date_format(date_create($_POST["date1"]),"m-d-Y")." to ".date_format(date_create($_POST["date2"]),"m-d-Y"));

	$spreadsheet->getActiveSheet()->mergeCells("A1:K1");
	$spreadsheet->getActiveSheet()->mergeCells("A2:K2");
	$spreadsheet->getActiveSheet()->mergeCells("A3:K3");

	$cntrow = 3;

	$cntrow++;
	$cntrow++;
	// Add some data
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cntrow, 'Date')
		->setCellValue('B'.$cntrow, 'PR No.')
		->setCellValue('C'.$cntrow, 'Section')
		->setCellValue('D'.$cntrow, 'Item Code')
		->setCellValue('E'.$cntrow, 'Part No.')
		->setCellValue('F'.$cntrow, 'Description')
		->setCellValue('G'.$cntrow, 'Remarks')
		->setCellValue('H'.$cntrow, 'UOM')
		->setCellValue('I'.$cntrow, 'PR Qty')
		->setCellValue('J'.$cntrow, 'PO Qty')
		->setCellValue('K'.$cntrow, 'Balance');

	$spreadsheet->getActiveSheet()->getStyle('A1:K'.$cntrow)->getFont()->setBold(true);
	$spreadsheet->getActiveSheet()->getStyle('A1'.':K'.$cntrow)->getAlignment()->setHorizontal('center');

	//start ng details//
	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$rpt = $_POST["slesections"];
	$postz = $_POST["sleposted"];

	//echo $postz;
	if($postz!==""){
		$qry = " and b.lapproved=".$postz;
	}
	else{
		$qry = "";
	}

	$qrytyp = "";
	if($rpt==""){
		$qrytyp = "";
	}else{
		$qrytyp = " and b.locations_id='$rpt'";
	}

	$arrPO = array();
	$result=mysqli_query($con,"Select A.creference, A.nrefident, A.citemno, sum(A.nqty) as nqty From purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono where A.compcode='".$company."' and B.lcancelled=0 and B.lapproved=1 and B.lvoid=0 Group By A.creference, A.nrefident, A.citemno");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$arrPO[] = $row;
	}

	$sql = "select b.dneeded as dcutdate, a.ctranno, b.locations_id, c.cdesc as csection, a.nident, a.citemno, a.citemdesc, a.cpartdesc, a.cunit, a.nqty, a.cremarks
	From purchrequest_t a
	left join purchrequest b on a.compcode=b.compcode and a.ctranno=b.ctranno
	left join locations c on b.compcode=c.compcode and b.locations_id=c.nid
	where a.compcode='".$company."' and b.dneeded between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid=0" .$qry. $qrytyp. "
	order by b.dneeded, a.ctranno";
	$result=mysqli_query($con,$sql);

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		
		//find PO reference
		$POQty = 0;
		foreach($arrPO as $rowPO){
			if($row['ctranno']==$rowPO['creference'] && $row['citemno']==$rowPO['citemno'] && $row['nident']==$rowPO['nrefident']){
				$POQty = $POQty + floatval($rowPO['nqty']);
			}
		}
		
		$cxBal = floatval($row['nqty']) - floatval($POQty);

		if($cxBal > 0){

			$cntrow++;
			$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$cntrow, $row['dcutdate'])
				->setCellValue('B'.$cntrow, $row['ctranno'])
				->setCellValue('C'.$cntrow, $row['csection'])
				->setCellValue('D'.$cntrow, $row['citemno'])
				->setCellValue('E'.$cntrow, $row['cpartdesc'])
				->setCellValue('F'.$cntrow, $row['citemdesc'])
				->setCellValue('G'.$cntrow, $row['cremarks'])
				->setCellValue('H'.$cntrow, $row['cunit'])
				->setCellValue('I'.$cntrow, $row['nqty'])
				->setCellValue('J'.$cntrow, $POQty)
				->setCellValue('K'.$cntrow, $cxBal);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		}
	}

	$sheet = $spreadsheet->getActiveSheet();
	foreach ($sheet->getColumnIterator() as $column) {
		$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
	}

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('PRMonitoring');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="PRMonitoring.xlsx"');
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