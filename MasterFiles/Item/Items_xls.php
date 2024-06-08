<?php
if(!isset($_SESSION)){
	session_start();
}
$_SESSION['pageid'] = "Items_Export";

require_once  "../../vendor2/autoload.php";
require_once "../../Connection/connection_string.php";
include('../../include/denied.php');
include('../../include/access2.php');

//use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Items')
    ->setSubject('Items')
    ->setDescription('Items, generated using Myx Financials.')
    ->setKeywords('myx_financials items')
    ->setCategory('Myx Financials Items');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Item Code')
    ->setCellValue('B1', 'Description')
		->setCellValue('C1', 'UOM')
		->setCellValue('D1', 'Type')
		->setCellValue('E1', 'Classification')
		->setCellValue('F1', 'Status');

$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];


	$xsql = "Select * From items where compcode='$company'";

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
    ->setCellValue('A'.$cnt, $row['cpartno'])
    ->setCellValue('B'.$cnt, $row['citemdesc'])
		->setCellValue('C'.$cnt, $row['cunit'])
		->setCellValue('D'.$cnt, $row['ctype'])
		->setCellValue('E'.$cnt, $row['cclass'])
		->setCellValue('F'.$cnt, $row['cstatus']);

	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Items');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ItemsList.xlsx"');
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