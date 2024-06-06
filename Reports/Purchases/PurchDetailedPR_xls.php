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
    ->setCellValue('A2', 'Purchases Order Report '.date_format(date_create($_POST["date1"]),"m-d-Y")." to ".date_format(date_create($_POST["date2"]),"m-d-Y"));

	$spreadsheet->getActiveSheet()->mergeCells("A1:M1");
	$spreadsheet->getActiveSheet()->mergeCells("A2:M2");

	$cntrow = 2;

	// Add some data

	$cntrow++;
	$cntrow++;
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cntrow, 'Date')
		->setCellValue('B'.$cntrow, 'Voucher No.')
		->setCellValue('C'.$cntrow, 'Links')
		->setCellValue('D'.$cntrow, 'Supplier')
		->setCellValue('E'.$cntrow, 'Code')
		->setCellValue('F'.$cntrow, 'Name')
		->setCellValue('G'.$cntrow, 'Description')
		->setCellValue('H'.$cntrow, 'UOM')
		->setCellValue('I'.$cntrow, 'Qty')
		->setCellValue('J'.$cntrow, 'Price')
		->setCellValue('K'.$cntrow, 'Amount')
		->setCellValue('L'.$cntrow, 'Currency')
		->setCellValue('M'.$cntrow, 'PO Due Date')
		->setCellValue('N'.$cntrow, 'Status');

	$spreadsheet->getActiveSheet()->getStyle('A1:N'.$cntrow)->getFont()->setBold(true);
	$spreadsheet->getActiveSheet()->getStyle('A1'.':N'.$cntrow)->getAlignment()->setHorizontal('center');

//start ng details//

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$rpt = $_POST["seltype"];
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
	$qrytyp = " and d.ctype='$rpt'";
}

$sql = "select b.dpodate as dcutdate, a.cpono as ctranno, b.ccode, c.cname, a.creference, a.citemno, a.cpartno, a.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross, b.lapproved, b.ccurrencycode, e.nintval as nterms
From purchase_t a
left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
left join groupings e on b.compcode=e.compcode and b.cterms=e.ccode
where a.compcode='".$company."' and b.dpodate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid=0" .$qry. $qrytyp. "
order by b.dpodate, a.cpono";


$result=mysqli_query($con,$sql);

$dxdate = "";
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$cntrow++;

	if(intval($row['nterms']) > 0){
		$dxdate = date('Y-m-d', strtotime($row['dcutdate']. ' + '.$row['nterms'].' days'));
	}else{
		$dxdate = $row['dcutdate'];
	}

	$spreadsheet->setActiveSheetIndex(0)
    	->setCellValue('A'.$cntrow, $row['dcutdate'])
    	->setCellValue('B'.$cntrow, $row['ctranno'])
		->setCellValue('C'.$cntrow, $row['creference'])
		->setCellValue('D'.$cntrow, $row['cname'])
		->setCellValue('E'.$cntrow, $row['citemno'])
		->setCellValue('F'.$cntrow, $row['cpartno'])
		->setCellValue('G'.$cntrow, $row['citemdesc'])
		->setCellValue('H'.$cntrow, $row['cunit'])
		->setCellValue('I'.$cntrow, $row['nqty'])
		->setCellValue('J'.$cntrow, $row['nprice'])
		->setCellValue('K'.$cntrow, $row['namount'])
		->setCellValue('L'.$cntrow, $row['ccurrencycode'])
		->setCellValue('M'.$cntrow, $dxdate)
		->setCellValue('N'.$cntrow, (($row['lapproved']==1) ? "APPROVED" : "PENDING"));

		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cntrow)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}

	$sheet = $spreadsheet->getActiveSheet();
	foreach ($sheet->getColumnIterator() as $column) {
		$sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
	}
	
// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Purchase_Detailed');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Purchase_Detailed.xlsx"');
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