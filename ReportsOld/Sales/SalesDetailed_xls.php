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
    ->setTitle('Sales Detailed')
    ->setSubject('Sales Detailed Report')
    ->setDescription('Sales Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A2', 'Date')
    ->setCellValue('B2', 'Invoice No.')
		->setCellValue('C2', 'Customer Type')
		->setCellValue('D2', 'Customer Code')
		->setCellValue('E2', 'Customer Name')
		->setCellValue('F2', 'Product')
		->setCellValue('G2', '')
		->setCellValue('H2', 'UOM')
		->setCellValue('I2', 'QTY')
		->setCellValue('J2', 'Price')
		->setCellValue('K2', 'Discount')
		->setCellValue('L2', 'Net Price')
		->setCellValue('M2', 'Amount');

$spreadsheet->getActiveSheet()->mergeCells("F2:G2");
$spreadsheet->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];
$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$itmtype = $_POST["seltype"];
$custype = $_POST["selcustype"];
$trantype = $_POST["seltrantype"]; 
$postedtran = $_POST["sleposted"];

$mainqry = "";
$finarray = array();

$qryitm = "";
if($itmtype!==""){
	$qryitm = " and c.ctype='".$itmtype."'";
}

$qrycust = "";
if($custype!==""){
	$qrycust = " and d.ccustomertype='".$custype."'";
}

$qryposted = "";
if($postedtran!==""){
	$qryposted = " and b.lapproved=".$postedtran."";
}

if($trantype=="Trade"){

	$result=mysqli_query($con,"select b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	order by a.ctranno, a.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	order by a.ctranno, a.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount, A.namount
	From (
		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."

		UNION ALL

		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
	) A 
	order by A.ctranno, A.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}
	
$salesno = "";
$remarks = "";
$invval = "";
$code = "";
$name= "";
$dateval="";
$classcode="";
$totAmount=0;	
$ngross = 0;
$cnt = 2;
foreach($finarray as $row)
{
	//if($salesno==""){
		//$salesno = $row['csalesno'];
	//}
	
	if($salesno!=$row['ctranno']){
		$invval = $row['ctranno'];
		$remarks = $row['cname'];
		$ccode = $row['ccode'];
		$crank = $row['typdesc'];
	//	$ngross = $row['ngross'];
		$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
		$classcode="class='rpthead'";
	}

	$netprice = floatval($row['nprice']) - floatval($row['ndiscount']);

		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $dateval)
    ->setCellValue('B'.$cnt, $invval)
		->setCellValue('C'.$cnt, $crank)
		->setCellValue('D'.$cnt, $ccode)
		->setCellValue('E'.$cnt, $remarks)
		->setCellValue('F'.$cnt, $row['citemno'])
		->setCellValue('G'.$cnt, strtoupper($row['citemdesc']))
		->setCellValue('H'.$cnt, $row['cunit'])
		->setCellValue('I'.$cnt, $row['nqty'])
		->setCellValue('J'.$cnt, $row['nprice'])
		->setCellValue('K'.$cnt, $row['ndiscount'])
		->setCellValue('L'.$cnt, $netprice)
		->setCellValue('M'.$cnt, $row['namount']);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + $row['namount'];
	}

	//total
	$cnt++;

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":L".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "GRAND TOTAL:")
    ->setCellValue('M'.$cnt, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":M".$cnt)->getFont()->setBold(true);
//End Details

//top
	$spreadsheet->getActiveSheet()->mergeCells("A1:L1");
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', "GRAND TOTAL:")
    ->setCellValue('M1', $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('M1')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A1")->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A1:M1")->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Sales_Detailed');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Sales_Detailed.xlsx"');
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