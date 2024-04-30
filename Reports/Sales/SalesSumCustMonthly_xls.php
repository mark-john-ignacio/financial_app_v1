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


	$company = $_SESSION['companyid'];

	//$date1 = $_POST["date1"];
	//$date2 = $_POST["date2"];

	$dateyr = $_POST["selmonth"];

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

	//and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') 
	//if($trantype=="Trade"){ 

		$sqlx = "select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
		From sales_t a	
		left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and YEAR(b.dcutdate) = '$dateyr' and b.lvoid=0 and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		order by d.ccustomertype, b.ccode, YEAR(b.dcutdate), MONTH(b.dcutdate)";

	/*}elseif($trantype=="Non-Trade"){

		$sqlx = "select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
		From ntsales_t a	
		left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and YEAR(b.dcutdate) = '$dateyr' and b.lvoid=0 and b.lcancelled=0
		".$qryitm.$qrycust.$qryposted."
		Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		order by d.ccustomertype, b.ccode, YEAR(b.dcutdate), MONTH(b.dcutdate)";

	}else{

		$sqlx = "Select A.mdate, A.ydate, A.compcode, A.ccode, A.cname, A.lapproved, A.ctype, A.typdesc, sum(A.nqty) as nqty, sum(A.nprice) as nprice
		From (
			select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
			From sales_t a	
			left join sales b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and YEAR(b.dcutdate) = '$dateyr' and b.lvoid=0 and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
			Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
			UNION ALL
			select MONTH(b.dcutdate) as mdate, YEAR(b.dcutdate) as ydate, a.compcode, b.ccode, d.ctradename as cname, b.lapproved, d.ccustomertype as ctype, e.cdesc as typdesc, sum(a.nqty) as nqty, sum(A.nprice*a.nqty) as nprice
			From ntsales_t a	
			left join ntsales b on a.ctranno=b.ctranno and a.compcode=b.compcode
			left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
			left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
			left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
			where a.compcode='$company' and YEAR(b.dcutdate) = '$dateyr' and b.lvoid=0 and b.lcancelled=0
			".$qryitm.$qrycust.$qryposted."
			Group By MONTH(b.dcutdate), YEAR(b.dcutdate), a.compcode, b.ccode, d.ctradename, b.lapproved, d.ccustomertype, e.cdesc
		) A 
		Group By A.mdate, A.ydate, A.compcode, A.ccode, A.cname, A.lapproved, A.ctype, A.typdesc 
		order by A.ctype, A.ccode, ydate, mdate";

	}*/

	//echo $sqlx;

	$mtnyr = array();
	$customers = array();

	$result=mysqli_query($con,$sqlx);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;

		$myrxc = $row['mdate']."/".$row['ydate'];
		if (!in_array($myrxc, $mtnyr)) {
			$mtnyr[] = $myrxc;
		}

		if (!in_array($row['ccode'], $customers)) {
			$customers[] = $row['ccode'];
		}

	}

	
	$sqlcustos = mysqli_query($con, "Select A.cempid, A.cname, A.ctradename, A.ccustomertype as ctype, B.cdesc as typdesc from customers A left join groupings B on A.ccustomertype=B.ccode and A.compcode=B.compcode and B.ctype='CUSTYP' where A.compcode='$company' and A.cempid in ('".implode("','",$customers)."') Order by A.ccustomertype, A.ctradename");

	asort($mtnyr);


	//Header
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2, 'Customer Type');
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 2, 'Customer Code');
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 2, 'Customer Name');

		$cols = 3;
		$mnthltot = array();
		foreach($mtnyr as $xmnt){
			$cols++;

			$mnth = explode("/",$xmnt);
			$mnthltot[$mnth[0].$mnth[1]] = 0;

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 2, date('F', mktime(0, 0, 0, $mnth[0], 10))." ".$mnth[1]);
		}

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 2, 'Total Amount');
	//end header


	//Start Details
	$cols = 3;
	$rows = 2;		
	while($row = mysqli_fetch_array($sqlcustos, MYSQLI_ASSOC))
	{
		$cols = 3;
		$rows++;

		$cxtotal = 0;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rows, $row['typdesc']);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $rows, $row['cempid']);
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $rows, $row['ctradename']);

		foreach($mtnyr as $rs8){
			$cols++;

			$mnth = explode("/",$rs8);

			$nprx = 0;
			foreach($finarray as $rs9){
				if($row['cempid']==$rs9['ccode'] && $mnth[0]==$rs9['mdate'] && $mnth[1]==$rs9['ydate']) {
					$nprx = $rs9['nprice'];
				}
			}

			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nprx);
			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

			$cxtotal = $cxtotal + $nprx;
			$mnthltot[$mnth[0].$mnth[1]] = $mnthltot[$mnth[0].$mnth[1]] + $nprx;

		}

		$cols++;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $cxtotal);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}

//End Details

//total at the top and bottom

//total sa bottom
$rows++;
$cols = 3;

$totPrice = 0;

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rows, "Grand Total");
$spreadsheet->getActiveSheet()->mergeCells("A".$rows.":C".$rows);

foreach($mtnyr as $rs8){
	$mnth = explode("/",$rs8);

	$cols++;
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $mnthltot[$mnth[0].$mnth[1]]);
	$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + floatval($mnthltot[$mnth[0].$mnth[1]]);
}

	$cols++;
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");


//total sa TOP
$rows = 1;
$cols = 3;

$totPrice = 0;

$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $rows, "Grand Total");
$spreadsheet->getActiveSheet()->mergeCells("A".$rows.":C".$rows);

foreach($mtnyr as $rs8){
	$mnth = explode("/",$rs8);

	$cols++;
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $mnthltot[$mnth[0].$mnth[1]]);
	$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	$totPrice = $totPrice + floatval($mnthltot[$mnth[0].$mnth[1]]);
}

	$cols++;
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $totPrice);
	$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");




// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('CustomerMonthly');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SalesSum_CustMonthly.xlsx"');
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