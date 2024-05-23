<?php
if(!isset($_SESSION)){
	session_start();
}
$_SESSION['pageid'] = "Suppliers_Export";

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
    ->setTitle('Suppliers')
    ->setSubject('Suppliers')
    ->setDescription('Suppliers, generated using Myx Financials.')
    ->setKeywords('myx_financials suppliers')
    ->setCategory('Myx Financials Suppliers');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Customer Code')
    ->setCellValue('B1', 'Registered Name')
		->setCellValue('C1', 'Business/Trade Name')
		->setCellValue('D1', 'Tin No')
		->setCellValue('E1', 'Type')
		->setCellValue('F1', 'Classification')
		->setCellValue('G1', 'Terms')
		->setCellValue('H1', 'Address')
		->setCellValue('I1', 'City')
		->setCellValue('J1', 'State')
		->setCellValue('K1', 'Country')
		->setCellValue('L1', 'ZIP Code')
		->setCellValue('M1', 'Status');

$spreadsheet->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];


	$xsql = "Select * From suppliers where compcode='$company'";

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
    ->setCellValue('A'.$cnt, $row['ccode'])
    ->setCellValue('B'.$cnt, $row['cname'])
		->setCellValue('C'.$cnt, $row['ctradename'])
		->setCellValue('D'.$cnt, $row['ctin'])
		->setCellValue('E'.$cnt, $row['csuppliertype'])
		->setCellValue('F'.$cnt, $row['csupplierclass'])
		->setCellValue('G'.$cnt, $row['cterms'])
		->setCellValue('H'.$cnt, $row['chouseno'])
		->setCellValue('I'.$cnt, $row['ccity'])
		->setCellValue('J'.$cnt, $row['cstate'])
		->setCellValue('K'.$cnt, $row['ccountry'])
		->setCellValue('L'.$cnt, $row['czip'])
		->setCellValue('M'.$cnt, $row['cstatus']);

	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Suppliers');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SuppliersList.xlsx"');
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