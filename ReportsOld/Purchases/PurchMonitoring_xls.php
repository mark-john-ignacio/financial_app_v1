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
    ->setTitle('PO Monitoring')
    ->setSubject('PO Monitoring Report')
    ->setDescription('Purchase Report, generated using Myx Financials.')
    ->setKeywords('myx_financials purchase_report')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Item Code')
    ->setCellValue('B1', 'Item Desc')
		->setCellValue('C1', 'UOM')
		->setCellValue('D1', 'Previous')
		->setCellValue('G1', 'Present')

		->setCellValue('D2', 'Supplier')
		->setCellValue('E2', 'Price')
		->setCellValue('F2', 'PO Date')
		->setCellValue('G2', 'Supplier')
		->setCellValue('H2', 'Price')
		->setCellValue('I2', 'PO Date');

$spreadsheet->getActiveSheet()->mergeCells("A1:A2");
$spreadsheet->getActiveSheet()->mergeCells("B1:B2");
$spreadsheet->getActiveSheet()->mergeCells("C1:C2");
$spreadsheet->getActiveSheet()->mergeCells("D1:F1");
$spreadsheet->getActiveSheet()->mergeCells("G1:I1");
$spreadsheet->getActiveSheet()->getStyle('A1:K2')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];

$date1 = $_POST["date1"];
$date2 = $_POST["date2"];

$datefil = $_POST["seltype"];

$selpost = $_POST["selpost"]; 

$qrypost = "";
if($selpost==1){
	$qrypost = " and b.lapproved = 1 ";
}

$sql = "select a.cpono as ctranno, b.".$datefil." as ddate, b.ccode, c.cname, a.nident, a.citemno, d.citemdesc, a.cunit, a.nqty, a.nprice, a.namount, d.ctype, e.cdesc as typedesc, DATE(b.ddate) as PODate
From purchase_t a
left join purchase b on a.compcode=b.compcode and a.cpono=b.cpono
left join suppliers c on b.compcode=c.compcode and b.ccode=c.ccode
left join items d on a.compcode=d.compcode and a.citemno=d.cpartno
left join groupings e on d.compcode=e.compcode and d.ctype=e.ccode and e.ctype='ITEMTYP'
where a.compcode='".$company."' and DATE(b.".$datefil.") between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')".$qrypost."
order by d.ctype, a.citemno, b.".$datefil." DESC";

//echo $sql;

$result=mysqli_query($con,$sql);

$rowxsx = array();
	$itmslist = array();
	$itmcode = "";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))	
	{
		$rowxsx[] = $row;

		if($itmcode!==$row['citemno']){
			$itmslist[] = array('citemno' => $row['citemno'], 'cdesc' => $row['citemdesc'], 'cunit' => $row['cunit'], 'ctype' => $row['ctype'], 'typedesc' => $row['typedesc']);

			$itmcode = $row['citemno'];
		}
		
	}

	
	$classcode="";
	$classdesc="";
	$TOTPOAmt=0;	
	$TOTSIAmt=0;
	$ngross = 0;

	$cnt = 2;
	foreach($itmslist as $row)
	{

		$cntr = 0;
		$Supp1 = "";
		$Price1 = "";
		$Date1 = "";

		$Supp2 = "";
		$Price2 = "";
		$Date2 = "";
		foreach($rowxsx as $xxrow){
			if($xxrow['citemno']==$row['citemno']){
				$cntr++;

				if($cntr==1){
					$Supp1 = $xxrow['cname'];
					$Price1 = $xxrow['nprice'];
					$Date1 = $xxrow['PODate'];
				}elseif($cntr==2){
					$Supp2 = $xxrow['cname'];
					$Price2 = $xxrow['nprice'];
					$Date2 = $xxrow['PODate'];

					break;
				}
			}

		}

		if(($Price1!=="" && $Price2!=="") && $Price1!==$Price2){
			$cnt++;

			if($classcode!==$row['ctype']){
				$cnt++;

				$classcode=$row['ctype'];
				$classdesc=$row['typedesc'];
	
				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('A'.$cnt, $classdesc);			
			}

			if(floatval($Price1) > floatval($Price2)){
				$xcstst = "DEC";
			}else{
				$xcstst = "INC";
			}

			if(floatval($Price1) > floatval($Price2)){
				$Decrease = floatval($Price1) - floatval($Price2);
				$Decrease = ($Decrease/ floatval($Price1)) * 100;

				$xcststnum = number_format($Decrease,2)."%";
			}else{
				$Increase = floatval($Price2) - floatval($Price1);
				$Increase = ($Increase/ floatval($Price2)) * 100;

				$xcststnum = number_format($Increase,2)."%";
			}

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['citemno'])
			->setCellValue('B'.$cnt, $row['cdesc'])
			->setCellValue('C'.$cnt, $row['cunit'])
			->setCellValue('D'.$cnt, $Supp1)
			->setCellValue('E'.$cnt, $Price1)
			->setCellValue('F'.$cnt, $Date1)
			->setCellValue('G'.$cnt, $Supp2)
			->setCellValue('H'.$cnt, $Price2)
			->setCellValue('I'.$cnt, $Date2)
			->setCellValue('J'.$cnt, $xcstst)
			->setCellValue('K'.$cnt, $xcststnum);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		}
	}


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('PO_Monitoring');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PO_Monitoring.xlsx"');
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