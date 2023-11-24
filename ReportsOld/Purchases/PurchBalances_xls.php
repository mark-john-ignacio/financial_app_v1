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
    ->setTitle('PO Balances')
    ->setSubject('PO Balances Report')
    ->setDescription('Purchase Report, generated using Myx Financials.')
    ->setKeywords('myx_financials purchase_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'PO No.')
    ->setCellValue('B1', 'PO Date')
		->setCellValue('C1', 'Supplier Code')
		->setCellValue('D1', 'Supplier Name')
		->setCellValue('E1', 'Product')
		->setCellValue('F1', '')
		->setCellValue('G1', 'UOM')
		->setCellValue('H1', 'PO Qty')
		->setCellValue('I1', 'Total RR Qty')
		->setCellValue('J1', 'Variance');

$spreadsheet->getActiveSheet()->mergeCells("E1:F1");
$spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$datefil = $_POST["seltype"];

$arrPO = array();
$result=mysqli_query($con,"Select A.creference, A.nrefidentity, A.citemno, sum(A.nqty) as nqty From receive_t A left join receive B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='".$company."' and B.lcancelled=0 Group By A.creference, A.nrefidentity, A.citemno");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}

$sql = "select a.cpono as ctranno, b.".$datefil." as ddate, b.ccode, c.cname, a.nident, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
From purchase_t a
left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
where a.compcode='".$company."' and DATE(b.".$datefil.") between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lapproved = 1
order by b.".$datefil.", a.cpono";

$result=mysqli_query($con,$sql);

$cnt = 1;

$salesno = "";
$remarks = "";
$invval = "";
$code = "";
$name= "";
$dateval="";
$classcode="";
$TOTPOAmt=0;	
$TOTSIAmt=0;
$ngross = 0;
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{

	//find PO reference
	$RRQty = 0;
	foreach($arrPO as $rowPO){
		if($rowPO['creference']==$row['ctranno'] && $rowPO['citemno']==$row['citemno'] && $rowPO['nrefidentity']==$row['nident']){
			$RRQty = $rowPO['nqty'];
		}
	}

	if(floatval($row['nqty'])!==floatval($RRQty)){

		$cnt++;

		$dvariance = floatval($row['nqty'])-floatval($RRQty);
		$dateval= date_format(date_create($row['ddate']),"m/d/Y");

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $row['ctranno'])
    ->setCellValue('B'.$cnt, $dateval)
		->setCellValue('C'.$cnt, $row['ccode'])
		->setCellValue('D'.$cnt, $row['cname'])
		->setCellValue('E'.$cnt, $row['citemno'])
		->setCellValue('F'.$cnt, $row['citemdesc'])
		->setCellValue('G'.$cnt, $row['cunit'])
		->setCellValue('H'.$cnt, $row['nqty'])
		->setCellValue('I'.$cnt, $RRQty)
		->setCellValue('J'.$cnt, $dvariance);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}

}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('PO_Balances');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PO_Balances.xlsx"');
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