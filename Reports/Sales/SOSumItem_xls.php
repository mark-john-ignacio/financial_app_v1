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
    ->setTitle('Job Order Summary')
    ->setSubject('Job Order Summary Report')
    ->setDescription('Job Order Report, generated using Myx Financials.')
    ->setKeywords('myx_financials job_order_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Item Class')
    ->setCellValue('B1', 'Product')
    ->setCellValue('C1', '')
    ->setCellValue('D1', 'UOM')
		->setCellValue('E1', 'Qty');

$spreadsheet->getActiveSheet()->mergeCells("B1:C1");
$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];
$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];

	$txtCustID = $_POST["txtCustID"];
	$itmtype = $_POST["seltype"];
	$itmclass = $_POST["seliclass"];
	$custype = $_POST["selcustype"];
	$trantype = $_POST["seltrantype"]; 
	$postedtran = $_POST["sleposted"];

	$mainqry = "";
	$finarray = array();

	$qryitm = "";
	if($txtCustID!=""){
		$qryitm = $qryitm." and b.ccode='".$txtCustID."'";
	}

	if($itmtype!=""){
		$qryitm = $qryitm." and c.ctype='".$itmtype."'";
	}

	if($itmclass!=""){
		$qryitm = $qryitm." and c.cclass='".$itmclass."'";
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

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From so_t a	
	left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
	order by  c.cclass, c.citemdesc");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}elseif($trantype=="Non-Trade"){

	$result=mysqli_query($con,"select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntso_t a	
	left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
	order by c.cclass, c.citemdesc");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}

}else{
	$result=mysqli_query($con,"Select A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.cclass, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
	From (
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From so_t a	
	left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc
	UNION ALL
	select a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
	From ntso_t a	
	left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on c.cclass=e.ccode and c.compcode=e.compcode and e.ctype='ITEMCLS'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
	".$qryitm.$qrycust.$qryposted."
	Group By a.compcode, a.citemno, c.citemdesc, a.cunit, b.lapproved, c.cclass, e.cdesc) A Group By A.compcode, A.citemno, A.citemdesc, A.cunit, A.lapproved, A.cclass, A.typdesc order by A.cclass, A.citemdesc");
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
	}
}

$ts1 = strtotime($date1);
$ts2 = strtotime($date2);

$year1 = date('Y', $ts1);
$year2 = date('Y', $ts2);

$month1 = date('m', $ts1);
$month2 = date('m', $ts2);

$mnths = (($year2 - $year1) * 12) + ($month2 - $month1);

$mnths = $mnths + 1;
		
	$cnt = 1;
	foreach($finarray as $row)
	{
		$cnt++;
		$spreadsheet->setActiveSheetIndex(0)
    	->setCellValue('A'.$cnt, $row['typdesc'])
    	->setCellValue('B'.$cnt, strtoupper($row['citemno']))
    	->setCellValue('C'.$cnt, $row['citemdesc'])
    	->setCellValue('D'.$cnt, $row['cunit'])
			->setCellValue('E'.$cnt, round($row['nqty'],2));

		$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	}

	//total
	$cnt++;


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Job_Order_Summary_Per_Item');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Job_Order_Summary_Per_Item.xlsx"');
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