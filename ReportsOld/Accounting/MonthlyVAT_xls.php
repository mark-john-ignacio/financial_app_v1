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
    ->setTitle('BIR Monthly VAT Report')
    ->setSubject('BIR Monthly VAT Report')
    ->setDescription('BIR Monthly VAT Report, generated using Myx Financials.')
    ->setKeywords('myx_financials bir_monthly_vat')
    ->setCategory('Myx Financials Report');

// Add some data
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'Trans No.')
		->setCellValue('C1', 'SI Series')
		->setCellValue('D1', 'DR')
		->setCellValue('E1', 'PO')
		->setCellValue('F1', 'TIN')
		->setCellValue('G1', 'Address')
		->setCellValue('H1', 'Name')
		->setCellValue('I1', 'Store Branch')
		->setCellValue('J1', 'Total')
		->setCellValue('K1', 'Gross')
		->setCellValue('L1', '12% VAT')
		->setCellValue('M1', 'NET');
$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true);

//start ng details//
$company = $_SESSION['companyid'];

$dte = explode("/",$_POST["date1"]);
$dtemo = $dte[0];
$dteyr = $dte[1];

$stat = $_POST["selstat"];


$monthNum  = intval($dtemo);
$dateObj   = DateTime::createFromFormat('!m', $monthNum);
$monthName = $dateObj->format('F');

$qry = "";
$varmsg = "";


$refSOPO = array();

//select * DR in SI
@$arrsalest = array();
$residet = mysqli_query($con,"Select creference, ctranno from sales_t Where compcode = '$company'");
while($row = mysqli_fetch_array($residet, MYSQLI_ASSOC)){
  @$arrsalest[] = $row;
}

//select * DR in SI
@$arrdrt = array();
$redrs = mysqli_query($con,"Select creference, ctranno from dr_t Where compcode = '$company'");
while($row = mysqli_fetch_array($redrs, MYSQLI_ASSOC)){
  @$arrdrt[] = $row;
}

//select * SO
@$arrsopo = array();
$residet = mysqli_query($con,"Select ctranno, cpono from so Where compcode = '$company'");
while($row = mysqli_fetch_array($residet, MYSQLI_ASSOC)){
  @$arrsopo[] = $row;
}

$qrystat = "";
if($stat!==""){
  $qrystat = " and A.lapproved=".$stat;
}

$sql = "Select a.ctranno, a.csiprintno, a.dcutdate, b.ctin, b.cname, b.ctradename, a.ngross, a.nnet, a.nvat, b.chouseno, b.ccity, b.cstate, b.ccountry
From sales a left join customers b on a.compcode = b.compcode and a.ccode=b.cempid
Where a.compcode = '$company' and DATE_FORMAT(a.dcutdate, '%m/%Y') = '".$_POST["date1"]."'
and a.lcancelled=0 ". $qrystat . " Order By a.dcutdate, a.ctranno";

$result=mysqli_query($con,$sql);
	
$cnt=1;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
	
		$address = $row['chouseno'];
    if($row['ccity']!==""){
      $address  = $address. ", ". $row['ccity'];
    }
    if($row['cstate']!==""){
      $address  = $address. ", ". $row['cstate'];
    }
    if($row['ccountry']!==""){
      $address  = $address. ", ". $row['ccountry'];
    }

		$cnt++;


				$drlist = array();
				$x = array();

        foreach(@$arrsalest as $rts){
          if($row['ctranno']==$rts['ctranno']){
            $drlist[] = $rts['creference'];
          }
        }
        if(count($drlist)>0){
          $x = array_unique($drlist);

            //findreferenceSO
            $refSOss = array();
            foreach(@$arrdrt as $sord){
              if(in_array($sord['ctranno'], $x)){
                $refSOss[] = $sord['creference'];
              }
            }

            $y = array_unique($refSOss);

            //findreferencePO
            foreach(@$arrsopo as $sorefs){
              if(in_array($sorefs['ctranno'], $y)){
                $refSOPO[] = $sorefs['cpono'];
              }
            }
        }else{
          echo "";
        }


		$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A'.$cnt, $row['dcutdate'])
    ->setCellValue('B'.$cnt, $row['ctranno'])
		->setCellValue('C'.$cnt, $row['csiprintno'])
		->setCellValue('D'.$cnt, implode(", ", $x))
		->setCellValue('E'.$cnt, implode(", ", $refSOPO))
		->setCellValue('F'.$cnt, $row['ctin'])
		->setCellValue('G'.$cnt, $address)
		->setCellValue('H'.$cnt, $row['cname'])
		->setCellValue('I'.$cnt, ($row['cname']!==$row['ctradename']) ? $row['ctradename'] : "")
		->setCellValue('J'.$cnt, $row['ngross'])
		->setCellValue('K'.$cnt, $row['ngross'])
		->setCellValue('L'.$cnt, $row['nvat'])
		->setCellValue('M'.$cnt, $row['nnet']);

		$spreadsheet->setActiveSheetIndex(0)->getStyle('J'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('K'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('L'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		$spreadsheet->setActiveSheetIndex(0)->getStyle('M'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

		$refSOPO = array();
	}

//End Details


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('BIR_Monthly_VAT_Report');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="BIR_Monthly_VAT_Report.xlsx"');
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