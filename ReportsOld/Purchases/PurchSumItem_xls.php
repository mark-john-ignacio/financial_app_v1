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
    ->setCellValue('A2', 'Classification')
    ->setCellValue('B2', 'Product')
		->setCellValue('D2', 'UOM')
		->setCellValue('E2', 'Ave. Purchase / Month')
		->setCellValue('F2', 'Qty')
		->setCellValue('G2', 'Total Amount');

	$spreadsheet->getActiveSheet()->mergeCells("B2:C2");
	$spreadsheet->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);

	//start ng details//
	$company = $_SESSION['companyid'];

	$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$postz = $_POST["sleposted"];

if($postz!==""){
	$qry = " and b.lapproved=".$postz;
}
else{
	$qry = "";
}

$arrPO = array();
$result=mysqli_query($con,"Select A.cpono, A.nident, A.citemno, A.nprice, A.namount From purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono where A.compcode='".$company."' and B.lcancelled=0 and B.lapproved=1");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}


$arrSI = array();
$result=mysqli_query($con,"Select A.creference, A.nrefidentity, A.citemno, A.nprice, A.namount From suppinv_t A left join suppinv B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='".$company."' and B.lcancelled=0");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrSI[] = $row;
}

$mnths = (int)abs((strtotime($date1) - strtotime($date2))/(60*60*24*30)) + 1;
//$rpt = $_POST["selrpt"];

$sql = "select A.cclass, A.cdesc, A.citemno, A.citemdesc, A.cunit, sum(A.nqty) as nqty, sum(A.nprice*A.nqty) as nprice, sum(A.ncost*A.nqty) as ncost
FROM
(
select d.cclass, c.cdesc, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, 0 as ncost
From suppinv_t a
left join suppinv b on a.ctranno=b.ctranno and a.compcode=b.compcode
left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
left join groupings c on d.cclass=c.ccode and a.compcode=c.compcode and c.ctype='ITEMCLS'
where a.compcode='$company' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0".$qry."
) A
group by A.cclass, A.cdesc,A.citemno, A.citemdesc, A.cunit
order by A.cclass, sum(a.nqty) DESC";

$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
						printf("Errormessage: %s\n", mysqli_error($con));
	} 
	
	$cnt = 2;
	$nAve = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$cnt++;

		$nAve = floatval($row['nqty']) / $mnths;

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['cdesc'])
			->setCellValue('B'.$cnt, $row['citemno'])
			->setCellValue('C'.$cnt, $row['citemdesc'])
			->setCellValue('D'.$cnt, $row['cunit'])
			->setCellValue('E'.$cnt, $nAve)
			->setCellValue('F'.$cnt, $row['nqty'])
			->setCellValue('G'.$cnt, $row['nprice']);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$totAmount = $totAmount + $row['nprice'];
		}

		$cnt++;
		//total
		$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":F".$cnt);
		$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
			->setCellValue('F'.$cnt, $totAmount);
		$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
		$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":G".$cnt)->getFont()->setBold(true);


//top
$cnt = 1;
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":F".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
    ->setCellValue('F'.$cnt, $totAmount);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":G".$cnt)->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Purchases_Sum_Item');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Purchases_Sum_Item.xlsx"');
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