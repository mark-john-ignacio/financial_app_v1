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
    ->setTitle('Chart of Accounts')
    ->setSubject('Chart of Accounts')
    ->setDescription('Chart of Accounts, generated using Myx Financials.')
    ->setKeywords('myx_financials chart_of_accounts')
    ->setCategory('Myx Financials Chart of Accounts');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Account No')
    ->setCellValue('B1', 'Description')
		->setCellValue('C1', 'Category')
		->setCellValue('D1', 'Type')
		->setCellValue('E1', 'Parent Code')
		->setCellValue('F1', 'Level');

$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];


	$xsql = "Select * From accounts where compcode='$company'";

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
    ->setCellValue('A'.$cnt, $row['cacctid'])
    ->setCellValue('B'.$cnt, $row['cacctdesc'])
		->setCellValue('C'.$cnt, $row['ccategory'])
		->setCellValue('D'.$cnt, $row['ctype'])
		->setCellValue('E'.$cnt, $row['mainacct'])
		->setCellValue('F'.$cnt, $row['nlevel']);

	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('ChartOfAccounts');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ChartOfAccounts.xlsx"');
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