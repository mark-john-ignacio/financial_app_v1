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
    ->setTitle('Sales Summary')
    ->setSubject('Sales Summary Report')
    ->setDescription('Sales Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A2', 'Item Type')
    ->setCellValue('B2', 'Product')
    ->setCellValue('C2', '')
    ->setCellValue('D2', 'UOM')
		->setCellValue('E2', 'Ave. Sales / Month')
		->setCellValue('F2', 'Qty')
		->setCellValue('G2', 'Total Amount');

$spreadsheet->getActiveSheet()->mergeCells("B2:C2");
$spreadsheet->getActiveSheet()->getStyle('A2:G2')->getFont()->setBold(true);

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

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc
	order by sum(A.nprice*a.nqty) DESC");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc
	order by sum(A.nprice*a.nqty) DESC");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.ctype, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
	From (
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc
	UNION ALL
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.ctype=e.ccode and c.compcode=e.compcode and e.ctype='ITEMTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.ctype, e.cdesc) A Group By A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.ctype, A.typdesc order by sum(A.nprice) DESC");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}

	$mnths = (int)abs((strtotime($date1) - strtotime($date2))/(60*60*24*30)) + 1;
	
	$totPrice=0;	
	$cnt = 2;
	foreach($finarray as $row)
	{
		$cnt++;
		$aveval = floatval($row['nqty']) / $mnths;
		$spreadsheet->setActiveSheetIndex(0)
    	->setCellValue('A'.$cnt, $row['typdesc'])
    	->setCellValue('B'.$cnt, strtoupper($row['citemno']))
    	->setCellValue('C'.$cnt, $row['citemdesc'])
    	->setCellValue('D'.$cnt, $row['cunit'])
			->setCellValue('E'.$cnt, round($aveval,2))
			->setCellValue('F'.$cnt, round($row['nqty'],2))
			->setCellValue('G'.$cnt, round($row['nprice'],2));

		$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + $row['nprice'];
	}

	//total
	$cnt++;

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":F".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "GRAND TOTAL:")
    ->setCellValue('G'.$cnt, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":G".$cnt)->getFont()->setBold(true);
//End Details

//TOP
	$spreadsheet->getActiveSheet()->mergeCells("A1:F1");
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', "GRAND TOTAL:")
    ->setCellValue('G1', $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('G1')->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A1")->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A1:G1")->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Sales_Summary_Per_Item');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Sales_Summary_Per_Item.xlsx"');
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