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
    ->setTitle('SO vs DR vs SI')
    ->setSubject('SO vs DR vs SI Report')
    ->setDescription('Sales Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Delivery Date')
    ->setCellValue('B1', 'SO No.')
		->setCellValue('C1', 'Customer')
		->setCellValue('D1', 'Item Code')
		->setCellValue('E1', 'Item Desc')
		->setCellValue('F1', 'UOM')
		->setCellValue('G1', 'SO Qty')
		->setCellValue('H1', 'DR Qty')
		->setCellValue('I1', 'SI Qty')
		->setCellValue('J1', 'SI Price')
		->setCellValue('K1', 'Returned');

$spreadsheet->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];
$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$custype = $_POST["selcustype"];
$trantype = $_POST["seltrantype"]; 
$postedtran = $_POST["sleposted"];

$qrycust = "";
if($custype!==""){
	$qrycust = " and d.ccustomertype='".$custype."'";
}

$qryposted = "";
if($postedtran!==""){
	$qryposted = " and b.lapproved=".$postedtran."";
}

//if($trantype!==""){
	$xsql = "select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
	From so_t a	
	left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
	left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
	left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
	left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
	where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and b.lvoid=0
	".$qryposted.$qrycust."
	order by a.ctranno, a.nident";


/*}else{
	$xsql = "Select A.nident, A.dcutdate, A.ctranno, A.ctype, A.typdesc, A.ccode, A.cname, A.lapproved, A.citemno, A.citemdesc, A.cunit, A.nqty, A.nprice, A.namount
	From (
		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
		From so_t a	
		left join so b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and b.lvoid=0
		".$qryposted.$qrycust."

		UNION ALL

		select a.nident, b.dcutdate, a.ctranno, d.ccustomertype as ctype, e.cdesc as typdesc, b.ccode, d.ctradename as cname, b.lapproved, a.citemno, c.citemdesc, a.cunit, a.nqty, a.nprice, a.namount
		From ntso_t a	
		left join ntso b on a.ctranno=b.ctranno and a.compcode=b.compcode
		left join items c on a.citemno=c.cpartno and a.compcode=c.compcode
		left join customers d on b.ccode=d.cempid and b.compcode=d.compcode
		left join groupings e on d.ccustomertype=e.ccode and c.compcode=e.compcode and e.ctype='CUSTYP'
		where a.compcode='$company' and b.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled=0 and b.lvoid=0
		".$qryposted.$qrycust."
	) A 
	order by A.ctranno, A.nident";
	
}*/

$resDR=mysqli_query($con,"Select A.ctranno, A.nident, A.creference, A.crefident, A.citemno, A.nqty from dr_t A left join dr B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0");
$findr = array();
while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
	$findr[] = $row;
}

$resSI=mysqli_query($con,"Select creference, nrefident, citemno, A.nprice, sum(nqty) as nqty, sum(A.nqtyreturned) as nqtysr from sales_t A left join sales B on A.compcode=B.compcode and A.ctranno=B.ctranno where A.compcode='$company' and B.lapproved=1 and B.lvoid=0 Group By creference, nrefident, citemno, A.nprice");
$finsi = array();
while($row = mysqli_fetch_array($resSI, MYSQLI_ASSOC)){
	$finsi[] = $row;
}

//echo $xsql;

$result=mysqli_query($con,$xsql);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$finarray[] = $row;
}

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$cnt = 1;
	foreach($finarray as $row)
	{

		$netprice = 0;

		$edrqty = 0;
		$drnos = array();
		$dridents = array();
		foreach($findr as $drow){
			if($drow['creference']==$row['ctranno'] && $drow['citemno']==$row['citemno'] && $drow['crefident']==$row['nident']){
				$edrqty = $edrqty + floatval($drow['nqty']);
				$drnos[] = $drow['ctranno'];
				$dridents[] = $drow['nident'];
			}
		}

		$esiqty = 0;
		foreach($finsi as $srow){
			if(in_array($srow['creference'], $drnos) && in_array($srow['nrefident'], $dridents) && $srow['citemno']==$row['citemno']){
				$esiqty = $esiqty + floatval($srow['nqty']);
				$esiqtyret = $esiqtyret + floatval($srow['nqtysr']);
				$esiprice = $srow['nprice'];
				break;
			}
		}

		if($salesno!=$row['ctranno']){
			$invval = $row['ctranno'];
			$remarks = $row['cname'];
			$ccode = $row['ccode'];
			$crank = $row['typdesc'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}

		$cnt++;

		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $dateval)
    ->setCellValue('B'.$cnt, $invval)
		->setCellValue('C'.$cnt, $remarks)
		->setCellValue('D'.$cnt, $row['citemno'])
		->setCellValue('E'.$cnt, $row['citemdesc'])
		->setCellValue('F'.$cnt, $row['cunit'])
		->setCellValue('G'.$cnt, $row['nqty'])
		->setCellValue('H'.$cnt, $edrqty)
		->setCellValue('I'.$cnt, $esiqty)
		->setCellValue('J'.$cnt, $esiprice)
		->setCellValue('K'.$cnt, $esiqtyret);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('SODRSI');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SODRSI.xlsx"');
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