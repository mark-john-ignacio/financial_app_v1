<?php 
if(!isset($_SESSION)){
    session_start();
}
require_once  "../../../vendor2/autoload.php";
require_once "../../../Connection/connection_string.php";
require_once "../../../Model/helper.php";

//use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$company = $_SESSION['companyid'];
// $month = $_POST['months'];
// $year = $_POST['years'];

$month = date("m", strtotime($_POST['months']));
$year = date("Y", strtotime($_POST['years']));

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('SAWT')
    ->setSubject('SAWT')
    ->setDescription('SAWT, generated using Myx Financials.')
    ->setKeywords('myx_financials SAWT')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
	$query=mysqli_query($con,$sql);
	$comp = $query -> fetch_assoc();
    /**
     * Company Details
     */
    $spreadsheet->getActiveSheet()->getStyle('A11:K11')->getFont()->setBold(true);
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Summary Alphalist of Withholding Tax at Source')
        ->setCellValue('A6', 'TAX PAYER TRADE NAME: ' . $comp['compdesc'])
        ->setCellValue('A7', "TAX PAYER NAME: " . $comp['compname'])
        ->setCellValue('A8', "TAX PAYER TIN: " . TinValidation($comp['comptin']))
        ->setCellValue('A9', "TAX PAYER ADDRESS: " . $comp['compadd']);

    /**
     * List of Details
     */

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A11', "TRANSACTION DATE")
        ->setCellValue('B11', "CV REFERRENCE NO.")
        ->setCellValue('C11', "VENDOR TIN")
        ->setCellValue('D11', "VENDOR NAME")
        ->setCellValue('E11', "VENDOR ADDRESS ADDRESS")
        ->setCellValue('F11', "W/TAX CODE")
        ->setCellValue('G11', "W/TAX RATE")
        ->setCellValue('H11', "W/TAX BASE AMOUNT")
        ->setCellValue('I11', "W/TAX AMOUNT");
    

    $sql = "SELECT a.cewtcode, a.newtamt, a.ctranno, b.ngross, b.dcheckdate, c.cname, c.chouseno, c.ccity, c.ctin FROM paybill_t a 
        LEFT JOIN paybill b on a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c on a.compcode = c.compcode AND b.ccode = c.ccode
        WHERE a.compcode = '$company' AND MONTH(b.dcheckdate) = '$month' AND YEAR(b.dcheckdate) = '$year'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $index = 12;
        $TOTAL_GROSS =0; $TOTAL_CREDIT = 0;;
        while($row = $query -> fetch_array(MYSQLI_ASSOC)){
            
            $code = $row['cewtcode'];
            $credit = $row['newtamt'];
            if(strlen($code) != 0 && $credit != 0){
                $fullAddress = stringValidation($row['chouseno']);
                if(trim($row['ccity']) != ""){
                    $fullAddress .= " ". stringValidation($row['ccity']);
                }
                $ewt = getEWT($code);
                if($ewt['valid']) {
                    $gross = $row['ngross'];
                    $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue("A$index", $row['dcheckdate'])
                        ->setCellValue("B$index", $row['ctranno'])
                        ->setCellValue("C$index", TinValidation($row['ctin']))
                        ->setCellValue("D$index", $row['cname'])
                        ->setCellValue("E$index", $fullAddress)
                        ->setCellValue("F$index", $ewt['code'])
                        ->setCellValue("G$index", $ewt['rate'])
                        ->setCellValue("H$index", $gross)
                        ->setCellValue("I$index", $credit);

                    $TOTAL_GROSS += floatval($gross); 
                    $TOTAL_CREDIT += floatval($credit); 
                    
                    $index++;
                }
            }
        }
        $lastindex = $index;
        $index += 2;

        /**
         * Total Amount of Details
         */
        $spreadsheet->getActiveSheet()->getStyle("A$index:K$index")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("H$index", "=SUM(H13:H$lastindex)")
        ->setCellValue("I$index", "=SUM(I13:I$lastindex)");
        // ->setCellValue("J$index", "=SUM(I$index:H$index)");

        $index += 2;
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","END OF REPORT");
    } else {
        $spreadsheet->setActiveSheetIndex(0)
        -> setCellValue("A15", "NO RECORD");
    }


	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('SAWT');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="SWAT_.xlsx"');
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
