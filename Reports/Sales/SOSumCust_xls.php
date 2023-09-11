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
    ->setTitle('JO Summary')
    ->setSubject('JO Summary Report')
    ->setDescription('JO Report, generated using Myx Financials.')
    ->setKeywords('myx_financials job_order_report')
    ->setCategory('Myx Financials Report');

	// Add some data
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Customer Type')
    ->setCellValue('B1', 'Customer')
		->setCellValue('D1', 'Total Amount');

	$spreadsheet->getActiveSheet()->mergeCells("B1:C1");
	$spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

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
	if($custype!=""){
		$qrycust = " and d.ccustomertype='".$custype."'";
	}

	$qryposted = "";
	if($postedtran!=""){
		$qryposted = " and b.lapproved=".$postedtran."";
	}

	if($trantype=="Trade"){

		$sqlx = "select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
		From so_t a	
		left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		order by d.ccustomertype, sum(A.namount) DESC";

	}elseif($trantype=="Non-Trade"){

		$sqlx = "select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
		From ntso_t a	
		left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		order by d.ccustomertype, sum(A.namount) DESC";

	}else{

		$sqlx = "Select A.compcode, A.ccode, A.cname, A.lapproved, A.ctype, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
		From (
			select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
			From so_t a	
			left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
			Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
			UNION ALL
			select a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.namount) as nprice
			From ntso_t a	
			left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
			Group By a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		) A 
		Group By A.compcode, A.ccode, A.cname, A.lapproved, A.ctype, A.typdesc order by A.ctype, sum(A.nprice) DESC";

	}

		$result=mysqli_query($con,$sqlx);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$finarray[] = $row;
		}
	
	$totPrice=0;	
	$cnt = 1;
	$cxdesc = "";
	foreach($finarray as $row)
	{
		$cnt++;
		
		$cxdesc = $row['cname'];
		if(intval($row['lapproved'])==0){
			$cxdesc = $cxdesc. " (Unposted)";
		}

		$spreadsheet->setActiveSheetIndex(0)
    	->setCellValue('A'.$cnt, $row['typdesc'])
    	->setCellValue('B'.$cnt, $row['ccode'])
    	->setCellValue('C'.$cnt, $cxdesc)
			->setCellValue('D'.$cnt, round($row['nprice'],2));

		$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + $row['nprice'];
	}

	//total
	$cnt++;

	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":C".$cnt);
	$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, "GRAND TOTAL:")
    ->setCellValue('D'.$cnt, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	$spreadsheet->setActiveSheetIndex(0)->getStyle("A".$cnt)->getAlignment()->setHorizontal('right');
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":D".$cnt)->getFont()->setBold(true);
//End Details


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Job_Order_Summary_Per_Customer');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Job_Order_Per_Customer.xlsx"');
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