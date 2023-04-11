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
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'Invoice No.')
		->setCellValue('C1', 'Product')
		->setCellValue('D1', '')
		->setCellValue('E1', 'UOM')
		->setCellValue('F1', 'QTY')
		->setCellValue('G1', 'Price')
		->setCellValue('H1', 'Discount')
		->setCellValue('I1', 'Net Price')
		->setCellValue('J1', 'Amount');

$spreadsheet->getActiveSheet()->mergeCells("C1:D1");
$spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];
$custid = $_POST["txtCustID"];
$cType = $_POST["seltype"];
$trantype = $_POST["seltrantype"];
$postedtran = $_POST["sleposted"];
//$cType = "Grocery";

$qrytp = "";
if($cType!==""){
	$qrytp = " and d.ctype='$cType'";
}

$qryposted = "";
if($postedtran!==""){
	$qryposted = " and b.lapproved=".$postedtran."";
}

if($trantype=="Trade"){
	$sql = "select A.dcutdate, A.ctranno as csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.ndiscount, A.namount
	FROM(
	select b.dcutdate, a.ctranno, b.ccode, IFNULL(c.ctradename,c.cname), a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
	From sales_t a
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	where a.compcode='$company' and b.ccode='$custid' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 ".$qrytp.$qryposted.") A order by A.dcutdate, A.ctranno";
}elseif($trantype=="Non-Trade"){
	$sql = "select A.dcutdate, A.ctranno as csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.ndiscount, A.nprice, A.namount
	FROM(
	select b.dcutdate, a.ctranno, b.ccode, IFNULL(c.ctradename,c.cname), a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
	From ntsales_t a
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
	left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
	where a.compcode='$company' and b.ccode='$custid' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 ".$qrytp.$qryposted.") A order by A.dcutdate, A.ctranno";
}else{
	$sql = "select A.dcutdate, A.ctranno as csalesno, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, A.nqty, A.ndiscount, A.nprice, A.namount
	FROM(
		select b.dcutdate, a.ctranno, b.ccode, IFNULL(c.ctradename,c.cname), a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From sales_t a
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
		left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
		where a.compcode='$company' and b.ccode='$custid' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 ".$qrytp.$qryposted."

		UNION ALL

		select b.dcutdate, a.ctranno, b.ccode, IFNULL(c.ctradename,c.cname), a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.ndiscount, a.namount
		From ntsales_t a
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join customers c on b.ccode=c.cempid and b.compcode=c.compcode
		left join items d on a.citemno=d.cpartno and a.compcode=d.compcode
		where a.compcode='$company' and b.ccode='$custid' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 ".$qrytp.$qryposted."
	) A order by A.dcutdate, A.ctranno";
}

$result=mysqli_query($con,$sql);
	
	$salesno = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$nGross=0;
	$cntr = 0;

$cnt = 1;
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$cnt++;

	$netprice = floatval($row['nprice']) - floatval($row['ndiscount']);

		if($salesno!=$row['csalesno']){
			$cntr = $cntr + 1;
			$invval = $row['csalesno'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
			//$nGross = $row['ngross'];
			
				if($cntr>1){
					
					$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":I".$cnt);
					$spreadsheet->setActiveSheetIndex(0)
						->setCellValue('A'.$cnt, "T O T A L:")
						->setCellValue('J'.$cnt, $nGross);
					$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
					$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
					$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":J".$cnt)->getFont()->setBold(true);

					$nGross = 0;
					$cnt++;
				}

		}

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $dateval)
    ->setCellValue('B'.$cnt, $invval)
		->setCellValue('c'.$cnt, $row['citemno'])
		->setCellValue('D'.$cnt, $row['citemdesc'])
		->setCellValue('E'.$cnt, $row['cunit'])
		->setCellValue('F'.$cnt, $row['nqty'])
		->setCellValue('G'.$cnt, $row['nprice'])
		->setCellValue('H'.$cnt, $row['ndiscount'])
		->setCellValue('I'.$cnt, $netprice)
		->setCellValue('J'.$cnt, $row['namount']);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$invval = "";
		$dateval="";		
		$classcode="";
		$nGross = $nGross + $row['namount'];		
		$salesno=$row['csalesno'];
		$totAmount = $totAmount + $row['namount'];
	}

	$cnt++;
	//total
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":I".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cnt, "T O T A L:")
		->setCellValue('J'.$cnt, $nGross);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":J".$cnt)->getFont()->setBold(true);

	$cnt++;
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":I".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "G R A N D  T O T A L:")
    ->setCellValue('J'.$cnt, $totAmount);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":J".$cnt)->getFont()->setBold(true);
//End Details


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Sales_Per_Customer');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Sales_Per_Customer.xlsx"');
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