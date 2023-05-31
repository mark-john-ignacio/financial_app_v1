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
    ->setCellValue('A1', 'Transaction Type')
    ->setCellValue('B1', 'Transaction No.')
		->setCellValue('C1', 'Date')
		->setCellValue('D1', 'Customer')
    ->setCellValue('E1', '')    
		->setCellValue('F1', 'Total Amount');

$spreadsheet->getActiveSheet()->mergeCells("D1:E1");
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);

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

	$result=mysqli_query($con,"select a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname) as cname, b.lapproved, 'Trade' as ctype, sum(A.nprice*a.nqty) as nprice
	From sales_t a	
	left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname), b.lapproved
	order by a.ctranno");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname) as cname, b.lapproved, 'Non-Trade' as ctype, sum(A.nprice*a.nqty) as nprice
	From ntsales_t a	
	left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname), b.lapproved
	order by a.ctranno");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.compcode, A.ctranno, A.dcutdate, A.ccode, A.cname, A.lapproved, A.ctype, sum(A.nprice) as nprice
	From (
		select a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname) as cname, b.lapproved, 'Trade' as ctype, sum(A.nprice*a.nqty) as nprice
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname), b.lapproved
		UNION ALL
		select a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname) as cname, b.lapproved, 'Non-Trade' as ctype, sum(A.nprice*a.nqty) as nprice
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By a.compcode, a.ctranno, b.dcutdate, b.ccode, IFNULL(d.ctradename,d.cname), b.lapproved
	) A 
	Group By A.compcode, A.ctranno, A.dcutdate, A.ccode, A.cname, A.lapproved, A.ctype order by A.ctype, A.ctranno");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}
	
	$totPrice=0;	
	$cnt = 1;
	foreach($finarray as $row)
	{
		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
    	->setCellValue('A'.$cnt, $row['ctype'])
    	->setCellValue('B'.$cnt, $row['ctranno'])
    	->setCellValue('C'.$cnt, $row['dcutdate'])
    	->setCellValue('D'.$cnt, $row['ccode'])
			->setCellValue('E'.$cnt, $row['cname'])
			->setCellValue('F'.$cnt, round($row['nprice'],2));

		$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + $row['nprice'];
	}

	//total
	$cnt++;

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":E".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "GRAND TOTAL:")
    ->setCellValue('F'.$cnt, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":F".$cnt)->getFont()->setBold(true);
//End Details


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Sales_Summary_Per_Transaction');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Sales_Summary_Per_Transaction.xlsx"');
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