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
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'WRR No.')
	->setCellValue('C1', 'Supplier Code')
	->setCellValue('D1', 'Supplier Name')
	->setCellValue('E1', 'Product')
	->setCellValue('F1', '')
	->setCellValue('G1', 'UOM')
	->setCellValue('H1', 'RR Qty')
	->setCellValue('I1', 'PO Price')
	->setCellValue('J1', 'PO Amount')
	->setCellValue('K1', 'SI Price')
	->setCellValue('L1', 'SI Amount')
	->setCellValue('M1', 'Currency');

$spreadsheet->getActiveSheet()->mergeCells("E1:F1");
$spreadsheet->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];

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

$arrPO = array();
$result=mysqli_query($con,"Select A.cpono, A.nident, A.citemno, A.nprice, A.namount, B.ccurrencycode From purchase_t A left join purchase B on A.compcode=B.compcode and A.cpono=B.cpono where A.compcode='".$company."' and B.lcancelled=0 and B.lapproved=1 and B.lvoid=0");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrPO[] = $row;
}


$arrSI = array();
$result=mysqli_query($con,"Select A.creference, A.nrefidentity, A.citemno, A.nprice, A.namount From suppinv_t A left join suppinv B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='".$company."' and B.lcancelled=0 and B.lvoid=0");
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$arrSI[] = $row;
}

$sql = "select b.dreceived as dcutdate, a.ctranno, b.ccode, c.cname, a.nident, a.nrefidentity, a.creference, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, b.cremarks, b.ngross
From receive_t a
left join receive b on a.compcode=b.compcode and a.ctranno=b.ctranno
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
where a.compcode='".$company."' and b.dreceived between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid=0" .$qry. $qrytyp. "
order by b.dreceived, a.ctranno";
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
	
	if($salesno!=$row['ctranno']){
		$invval = $row['ctranno'];
		$remarks = $row['cname'];
		$ccode = $row['ccode'];
		$ngross = $row['ngross'];
		$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
		$classcode="class='rpthead'";
	}

	//find PO reference
	$POPrice = 0;
	$POCurrCode = "";
	foreach($arrPO as $rowPO){
		if($rowPO['cpono']==$row['creference'] && $rowPO['citemno']==$row['citemno'] && $rowPO['nident']==$row['nrefidentity']){
			$POPrice = $rowPO['nprice'];
			$POCurrCode = $rowPO['ccurrencycode'];
		}
	}

	$POAmt = floatval($row['nqty']) * floatval($POPrice);


	//find Suppliers Invoice
	$SIPrice = 0;
	foreach($arrSI as $rowSI){
		if($rowSI['creference']==$row['ctranno'] && $rowSI['citemno']==$row['citemno'] && $rowSI['nrefidentity']==$row['nident']){
			$SIPrice = $rowSI['nprice'];
		}
	}

	$SIAmt = floatval($row['nqty']) * floatval($SIPrice);

		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $dateval)
    ->setCellValue('B'.$cnt, $invval)
		->setCellValue('C'.$cnt, $ccode)
		->setCellValue('D'.$cnt, $remarks)
		->setCellValue('E'.$cnt, $row['citemno'])
		->setCellValue('F'.$cnt, $row['citemdesc'])
		->setCellValue('G'.$cnt, $row['cunit'])
		->setCellValue('H'.$cnt, $row['nqty'])
		->setCellValue('I'.$cnt, $POPrice)
		->setCellValue('J'.$cnt, $POAmt)
		->setCellValue('K'.$cnt, $SIPrice)
		->setCellValue('L'.$cnt, $SIAmt)
		->setCellValue('L'.$cnt, $POCurrCode);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$invval = "";
		$remarks = "";
		$dateval="";
		$ccode = "";		
		$classcode="";		
		$ngross = "";
		$salesno=$row['ctranno'];
		$TOTPOAmt = $TOTPOAmt + floatval($POAmt);	
		$TOTSIAmt = $TOTSIAmt + floatval($SIAmt);

	}

	//total
	$cnt++;

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":F".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "GRAND TOTAL:")
		->setCellValue('G'.$cnt, "")
		->setCellValue('H'.$cnt, "")
		->setCellValue('I'.$cnt, "")
    ->setCellValue('J'.$cnt, $TOTPOAmt)
		->setCellValue('K'.$cnt, "")
		->setCellValue('L'.$cnt, $TOTSIAmt)
		->setCellValue('M'.$cnt, "");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":M".$cnt)->getFont()->setBold(true);
//End Details


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