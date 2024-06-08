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
		->setCellValue('A2', 'Customer Type')
		->setCellValue('B2', 'Customer Code')
		->setCellValue('C2', 'Customer Name')
		->setCellValue('D2', 'Product')
		->setCellValue('E2', '')
		->setCellValue('F2', 'UOM')
		->setCellValue('G2', 'QTY')
		->setCellValue('H2', 'Amount');

$spreadsheet->getActiveSheet()->mergeCells("D2:E2");
$spreadsheet->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);

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

//if($trantype=="Trade"){

	$result=mysqli_query($con,"select d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, a.citemno, c.citemdesc, a.cunit, sum(a.nqty) as nqty, sum(a.namount) as namount
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By d.ccustomertype, e.cdesc, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit
	order by a.ctranno, a.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

/*}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, a.citemno, c.citemdesc, a.cunit, sum(a.nqty) as nqty, sum(a.namount) as namount
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By d.ccustomertype, e.cdesc, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit
	order by a.ctranno, a.nident");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.ctype, A.typdesc, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit, sum(A.nqty) as nqty, sum(A.namount) as namount
	From (
		select d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, a.citemno, c.citemdesc, a.cunit, sum(a.nqty) as nqty, sum(a.namount) as namount
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By d.ccustomertype, e.cdesc, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit

		UNION ALL

		select d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, a.citemno, c.citemdesc, a.cunit, sum(a.nqty) as nqty, sum(a.namount) as namount
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lvoid=0 and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By d.ccustomertype, e.cdesc, b.ccode, d.ctradename, a.citemno, c.citemdesc, a.cunit
	) A 
	Group By A.ctype, A.typdesc, A.ccode, A.cname, A.citemno, A.citemdesc, A.cunit
	order by A.ctype, A.ccode");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}*/
	
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
	
	if($salesno!=$row['ccode']){
		$remarks = $row['cname'];
		$ccode = $row['ccode'];
		$crank = $row['typdesc'];
		$classcode="class='rpthead'";
	}

		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cnt, $crank)
		->setCellValue('B'.$cnt, $ccode)
		->setCellValue('C'.$cnt, $remarks)
		->setCellValue('D'.$cnt, $row['citemno'])
		->setCellValue('E'.$cnt, strtoupper($row['citemdesc']))
		->setCellValue('F'.$cnt, $row['cunit'])
		->setCellValue('G'.$cnt, $row['nqty'])
		->setCellValue('H'.$cnt, $row['namount']);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + $row['namount'];
	}

	//total
	$cnt++;

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":F".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "GRAND TOTAL:")
    ->setCellValue('H'.$cnt, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":H".$cnt)->getFont()->setBold(true);
//End Details

//top
	$spreadsheet->getActiveSheet()->mergeCells("A1:G1");
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', "GRAND TOTAL:")
    ->setCellValue('H1', $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('H1')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A1")->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('SalesSum_CustItem');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SalesSum_CustItem.xlsx"');
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