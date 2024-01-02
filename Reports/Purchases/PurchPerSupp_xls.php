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
    ->setCellValue('A2', 'Date')
    ->setCellValue('B2', 'Invoice No.')
		->setCellValue('C2', 'Product')
		->setCellValue('E2', 'UOM')
		->setCellValue('F2', 'Qty')
		->setCellValue('G2', 'Price')
		->setCellValue('H2', 'Amount');

	$spreadsheet->getActiveSheet()->mergeCells("C2:D2");
	$spreadsheet->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);

	//start ng details//
	$company = $_SESSION['companyid'];

	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$custid = $_POST["txtCustID"];
	$postedtran = $_POST["sleposted"];

	$qryposted = "";
	if($postedtran!==""){
		$qryposted = "and b.lapproved=".$postedtran."";
	}

	$sql = "select b.dreceived as dcutdate, a.ctranno as csalesno, b.ccode, c.cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.lapproved
	From suppinv_t a
	left join suppinv b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join suppliers c on b.ccode=c.ccode and a.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	where a.compcode='$company' and b.lvoid = 0 and b.ccode='$custid' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 ".$qryposted." order by b.dreceived, a.ctranno";

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	

	$invval = "";
	$totAmount=0;	

	$cnt = 2;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cnt++;

		$invval = $row['csalesno'];
		if($row['lapproved']==0){
			$invval = $invval . "<i>(Pending)</i>";
		}

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['dcutdate'])
			->setCellValue('B'.$cnt, $invval)
			->setCellValue('C'.$cnt, $row['citemno'])
			->setCellValue('D'.$cnt, $row['citemdesc'])
			->setCellValue('E'.$cnt, $row['cunit'])
			->setCellValue('F'.$cnt, $row['nqty'])
			->setCellValue('G'.$cnt, $row['nprice'])
			->setCellValue('H'.$cnt, $row['namount']);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$totAmount = $totAmount + $row['namount'];
		}

		$cnt++;
		//total
		$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":G".$cnt);
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, "T O T A L:")
			->setCellValue('H'.$cnt, $totAmount);
		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
		$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":H".$cnt)->getFont()->setBold(true);


//top
$cnt = 1;
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":G".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
    ->setCellValue('H'.$cnt, $totAmount);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":H".$cnt)->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Purchases_Per_Supplier');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Purchases_Per_Supplier.xlsx"');
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