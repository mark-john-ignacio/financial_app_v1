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
    ->setTitle('Sales Per Item')
    ->setSubject('Sales Per Item Report')
    ->setDescription('Sales Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A2', 'Date')
    ->setCellValue('B2', 'Invoice No.')
		->setCellValue('C2', 'Customer')
		->setCellValue('D2', '')
		->setCellValue('E2', 'QTY')
		->setCellValue('F2', 'UOM')
		->setCellValue('G2', 'Price')
		->setCellValue('H2', 'Discount')
		->setCellValue('I2', 'Net Price')
		->setCellValue('J2', 'Amount');

$spreadsheet->getActiveSheet()->mergeCells("C2:D2");
$spreadsheet->getActiveSheet()->getStyle('A2:J2')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$ItmID = $_POST["txtCustID"];
$cType = $_POST["seltype"];
$trantype = $_POST["seltrantype"];
$postedtran = $_POST["sleposted"];

$qrytp = "";
if($cType!==""){
	$qrytp = " and c.ccustomertype='$cType'";
}

$qryposted = "";
if($postedtran!==""){
	$qryposted = " and b.lapproved=".$postedtran."";
}

if($trantype=="Trade"){
	$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount,A.namount, A.lapproved
	FROM
	(
		select b.dcutdate, a.ctranno as csalesno, b.ccode, c.ctradename as cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice,a.ndiscount, a.namount, b.lapproved
		From sales_t a
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
		left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
		where a.compcode='$company' and a.citemno='$ItmID' and b.lcancelled=0".$qrytp.$qryposted."
	) A
	Where A.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
	order by A.dcutdate, A.csalesno";
}elseif($trantype=="Non-Trade"){
	$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount,A.namount, A.lapproved
	FROM
	(
		select b.dcutdate, a.ctranno as csalesno, b.ccode, c.ctradename as cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice,a.ndiscount, a.namount, b.lapproved
		From ntsales_t a
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
		left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
		where a.compcode='$company' and a.citemno='$ItmID' and b.lcancelled=0".$qrytp.$qryposted."
	) A
	Where A.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
	order by A.dcutdate, A.csalesno";
}else{
	$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount,A.namount, A.lapproved
	FROM
	(
		select b.dcutdate, a.ctranno as csalesno, b.ccode, c.ctradename as cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice,a.ndiscount, a.namount, b.lapproved
		From sales_t a
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
		left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
		where a.compcode='$company' and a.citemno='$ItmID' and b.lcancelled=0".$qrytp.$qryposted."

		UNION ALL

		select b.dcutdate, a.ctranno as csalesno, b.ccode, c.ctradename as cname, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice,a.ndiscount, a.namount, b.lapproved
		From ntsales_t a
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
		left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
		where a.compcode='$company' and a.citemno='$ItmID' and b.lcancelled=0".$qrytp.$qryposted."
	) A
	Where A.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
	order by A.dcutdate, A.csalesno";
}

$result=mysqli_query($con,$sql);
	
$ddate = "";
$invval = "";
$code = "";
$name= "";
$dateval="";
$classcode="";
$totAmount=0;	
$totQty=0;

$cnt = 2;
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$cnt++;

	$netprice = floatval($row['nprice']) - floatval($row['ndiscount']);
		
		if($ddate!=$row['dcutdate']){
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $dateval)
    ->setCellValue('B'.$cnt, ($row['lapproved']==0) ? $row['csalesno']."(Pending)" : $row['csalesno'])
		->setCellValue('c'.$cnt, $row['ccode'])
		->setCellValue('D'.$cnt, $row['cname'])
		->setCellValue('E'.$cnt, $row['nqty'])
		->setCellValue('F'.$cnt, $row['cunit'])
		->setCellValue('G'.$cnt, $row['nprice'])
		->setCellValue('H'.$cnt, $row['ndiscount'])
		->setCellValue('I'.$cnt, $netprice)
		->setCellValue('J'.$cnt, $row['namount']);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$dateval="";		
		$classcode="";		
		$ddate=$row['dcutdate'];
		$totAmount = $totAmount + $row['namount'];
		
		$totQty = $totQty + $row['nqty'];
	}

	$cnt++;
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":C".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
		->setCellValue('E'.$cnt, $totQty)
		->setCellValue('F'.$cnt, "")
		->setCellValue('G'.$cnt, "")
		->setCellValue('H'.$cnt, "")
		->setCellValue('I'.$cnt, "")
    ->setCellValue('J'.$cnt, $totAmount);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":J".$cnt)->getFont()->setBold(true);
//End Details

//top
$cnt = 1;
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":C".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
		->setCellValue('E'.$cnt, $totQty)
		->setCellValue('F'.$cnt, "")
		->setCellValue('G'.$cnt, "")
		->setCellValue('H'.$cnt, "")
		->setCellValue('I'.$cnt, "")
    ->setCellValue('J'.$cnt, $totAmount);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":J".$cnt)->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Sales_Per_Item');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Sales_Per_Item.xlsx"');
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
