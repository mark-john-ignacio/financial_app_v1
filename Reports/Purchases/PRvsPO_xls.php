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
		->setTitle('Purchase Request Vs Purchases Orders')
		->setSubject('Purchase Request Vs Purchases Orders Report')
		->setDescription('Purchase Report, generated using Myx Financials.')
		->setKeywords('myx_financials purchase_report')
		->setCategory('Myx Financials Report');


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

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$custid = $_POST["seltype"]; 

	//get ALL PO
	$xallPO = array();
	$sql = "select b.dpodate, a.cpono, b.ccode, c.cname, a.citemno, a.cpartno, a.citemdesc, a.cunit, a.nqty, f.cdesc as prepdept_desc, b.cremarks as hdr_remarks, b.lapproved, a.creference, a.nrefident
	From purchase_t a
	left join purchase b on a.cpono=b.cpono and a.compcode=b.compcode
	left join suppliers c on b.ccode=c.ccode and a.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	left join users e on b.cpreparedby=e.Userid
	left join locations f on e.cdepartment=f.nid and f.compcode='$company'
	where a.compcode='$company' and b.lvoid = 0 and b.dpodate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 order by b.dpodate, a.cpono";
	$result=mysqli_query($con,$sql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$xallPO[] = $row;
	}

	function getPO($cprno,$citemno,$citemident){
		global $xallPO;
		global $spreadsheet;
		global $cntrow;

		foreach($xallPO as $rs){
			if($cprno==$rs['creference'] && $citemno==$rs['citemno'] && intval($citemident)==intval($rs['nrefident'])){

				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$cntrow, $rs['dpodate'])
					->setCellValue('B'.$cntrow, $rs['cname'])
					->setCellValue('C'.$cntrow, $rs['cpono'])
					->setCellValue('D'.$cntrow, $rs['prepdept_desc'])
					->setCellValue('E'.$cntrow, "")
					->setCellValue('F'.$cntrow, $rs['citemno'])
					->setCellValue('G'.$cntrow, "")
					->setCellValue('H'.$cntrow, $rs['cpartno'])
					->setCellValue('I'.$cntrow, $rs['citemdesc'])
					->setCellValue('J'.$cntrow, "")
					->setCellValue('K'.$cntrow, $rs['nqty'])
					->setCellValue('L'.$cntrow, "0.00")
					->setCellValue('M'.$cntrow, $rs['hdr_remarks']);

				$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
				$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			}
		}
	}

	function getBalance($cprno,$citemno,$citemident){
		global $xallPO;

		$xbal = 0;
		foreach($xallPO as $rs){
			if($cprno==$rs['creference'] && $citemno==$rs['citemno'] && intval($citemident)==intval($rs['nrefident'])){
				$xbal = $xbal + floatval($rs['nqty']);
			}
		}

		return $xbal;
	}


	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', $compname);

	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A2', 'Documents Analysis Purchase Request Vs Purchases Orders '.date_format(date_create($_POST["date1"]),"m-d-Y")." to ".date_format(date_create($_POST["date2"]),"m-d-Y"));

	$spreadsheet->getActiveSheet()->mergeCells("A1:M1");
	$spreadsheet->getActiveSheet()->mergeCells("A2:M2");

	$cntrow = 2;

	if($_POST["seltype"]!=""){
		$spreadsheet->setActiveSheetIndex(0)
    		->setCellValue('A3', $compname);
		$spreadsheet->getActiveSheet()->mergeCells("A3:M3");

		$cntrow++;
	}

	// Add some data
	$cntrow++;
	$cntrow++;
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cntrow, 'Date')
		->setCellValue('B'.$cntrow, 'Supplier Name')
		->setCellValue('C'.$cntrow, 'Voucher No.')
		->setCellValue('D'.$cntrow, 'Department')
		->setCellValue('E'.$cntrow, 'Cost Center')
		->setCellValue('F'.$cntrow, 'Code')
		->setCellValue('G'.$cntrow, 'DR No.')
		->setCellValue('H'.$cntrow, 'Name')
		->setCellValue('I'.$cntrow, 'Description')
		->setCellValue('J'.$cntrow, 'Alias')
		->setCellValue('K'.$cntrow, 'Quantity')
		->setCellValue('L'.$cntrow, 'Balance')
		->setCellValue('M'.$cntrow, 'Remarks');

	$spreadsheet->getActiveSheet()->getStyle('A1:M'.$cntrow)->getFont()->setBold(true);
	$spreadsheet->getActiveSheet()->getStyle('A1'.':M'.$cntrow)->getAlignment()->setHorizontal('center');


	$xqry = "";
	if($custid!=""){
		$xqry = " and b.locations_id = ". $custid;
	}

	$sql = "select b.dneeded, a.ctranno, b.locations_id, c.cdesc as locations_desc, a.nident, a.citemno, a.cpartdesc, a.citemdesc, a.cremarks, a.cunit, a.nqty, e.cdesc as costcenter_desc, b.cremarks as hdr_remarks
	From purchrequest_t a
	left join purchrequest b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join locations c on b.locations_id=c.nid and a.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	left join locations e on a.location_id=e.nid and a.compcode=e.compcode
	where a.compcode='$company' and b.lvoid = 0 and b.lapproved = 1 and b.lcancelled = 0 and b.dneeded between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$xqry." order by b.dneeded, a.ctranno";

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$xnbalance = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cntrow++;

		$xnbalance = getBalance($row['ctranno'],$row['citemno'],$row['nident']);
		$xnbalance = floatval($row['nqty']) - floatval($xnbalance);

		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cntrow, $row['dneeded'])
			->setCellValue('B'.$cntrow, "")
			->setCellValue('C'.$cntrow, $row['ctranno'])
			->setCellValue('D'.$cntrow, $row['locations_desc'])
			->setCellValue('E'.$cntrow, $row['costcenter_desc'])
			->setCellValue('F'.$cntrow, $row['citemno'])
			->setCellValue('G'.$cntrow, "")
			->setCellValue('H'.$cntrow, $row['cpartdesc'])
			->setCellValue('I'.$cntrow, $row['citemdesc'])
			->setCellValue('J'.$cntrow, $row['cremarks'])
			->setCellValue('K'.$cntrow, $row['nqty'])
			->setCellValue('L'.$cntrow, $xnbalance)
			->setCellValue('M'.$cntrow, $row['hdr_remarks']);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		getPO($row['ctranno'],$row['citemno'],$row['nident']);

	}

	$sheet = $spreadsheet->getActiveSheet();
	foreach ($sheet->getColumnIterator() as $column) {
		$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('PRvsPO');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PRvsPO.xlsx"');
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