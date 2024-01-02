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
    ->setTitle('Purchase Detailed')
    ->setSubject('Purchase Detailed Report')
    ->setDescription('Purchase Report, generated using Myx Financials.')
    ->setKeywords('myx_financials purchase_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A2', 'Supplier Code')
    ->setCellValue('B2', 'Supplier Name')
		->setCellValue('C2', 'Total Amount');

	$spreadsheet->getActiveSheet()->getStyle('A2:C2')->getFont()->setBold(true);

	//start ng details//
	$company = $_SESSION['companyid'];

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$postz = $_POST["sleposted"];

	if($postz!==""){
		$qry = "and a.lapproved=".$postz;
	}
	else{
		$qry = "";
	}

	$sql = "select a.ccode, b.cname, sum(a.ngross) as namnt
	From suppinv a
	left join suppliers b on a.ccode=b.ccode
	where a.compcode='$company' and a.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and a.lcancelled=0 and a.lvoid=0 ".$qry." group by a.ccode, b.cname order by sum(a.ngross) DESC";

	//echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$cnt = 2;
	$totPriceG=0;	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cnt++;

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['ccode'])
			->setCellValue('B'.$cnt, $row['cname'])
			->setCellValue('C'.$cnt, $row['namnt']);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$totPriceG = $totPriceG + $row['namnt'];
		}

		$cnt++;
		//total
		$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
			->setCellValue('C'.$cnt, $totPriceG);
		$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
		$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);


//top
$cnt = 1;
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":B".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
    ->setCellValue('C'.$cnt, $totPriceG);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":C".$cnt)->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Purchases_Sum_Supp');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Purchases_Sum_Supp.xlsx"');
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