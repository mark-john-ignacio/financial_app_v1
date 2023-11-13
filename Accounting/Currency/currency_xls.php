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
    ->setTitle('Currency')
    ->setSubject('Currency')
    ->setDescription('Currency, generated using Myx Financials.')
    ->setKeywords('myx_financials currency list')
    ->setCategory('Myx Financials currency list');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Code')
    ->setCellValue('B1', 'Country')
		->setCellValue('C1', 'Name')
		->setCellValue('D1', 'Rate')
		->setCellValue('E1', 'Status');

$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];


	$xsql = "Select * From currency_rate where compcode='$company'";

	//echo $xsql;

	$result=mysqli_query($con,$xsql);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

	$cnt = 1;
	foreach($finarray as $row)
	{

		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $row['symbol'])
    ->setCellValue('B'.$cnt, $row['country'])
		->setCellValue('C'.$cnt, $row['unit'])
		->setCellValue('D'.$cnt, $row['rate'])
		->setCellValue('E'.$cnt, $row['cstatus']);

	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Items');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="CurrencyList.xlsx"');
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