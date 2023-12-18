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
$month_text = $_POST['months'];
// $month = $_POST['months'];
// $year = $_POST['years'];

$month = date("m", strtotime($_POST['months']));
$year = date("Y", strtotime($_POST['years']));

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('QUARTERLY ALPHALIST OF PAYEES')
    ->setSubject('QUARTERLY ALPHALIST OF PAYEESt')
    ->setDescription('QUARTERLY ALPHALIST OF PAYEES, generated using Myx Financials.')
    ->setKeywords('myx_financials QUARTERLY ALPHALIST OF PAYEES')
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
        ->setCellValue('A1', 'Attachment to BIR Form 1601-EQ')
        ->setCellValue('A2', 'QUARTERLY ALPHABETICAL LIST OF PAYEES SUBJECTED TO EXPANDED WITHHOLDING TAX & PAYEES WHOSE INCOME PAYMENTS ARE EXEMPT ')
        ->setCellValue('A3', "FOR THE QUARTER ENDING $month, $year")
        ->setCellValue('A6', 'TIN: ' . $comp['comptin'])
        ->setCellValue('A7', "WITHHOLDING AGENT'S NAME: " . $comp['compname']);
    /**
     * List of Details
     */

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A11', "SEQ")
        ->setCellValue('A12', "NO")
        ->setCellValue('B11', "TAXPAYER")
        ->setCellValue('B12', "IDENTIFICATION")
        ->setCellValue('B13', "NUMBER")
        ->setCellValue('C11', "CORPORATION")
        ->setCellValue('C12', "(Registered Name)")
        ->setCellValue('D11', "INDIVIDUAL")
        ->setCellValue('D12', "(Last Name, First Name, Middle Name)")
        ->setCellValue('E11', "ATC CODE")
        ->setCellValue('F11', "NATURE OF PAYMENT")
        ->setCellValue('K10', "1ST MONTH OF THE QUARTER")
        ->setCellValue('K11', "AMOUNT OF")
        ->setCellValue('K12', "INCOME PAYMENT")
        ->setCellValue('L11', "TAX RATE")
        ->setCellValue('M11', "AMOUNT OF")
        ->setCellValue('M12', "TAX WITHHELD")
        
        ->setCellValue('A14', "'(1)")
        ->setCellValue('B14', "'(2)")
        ->setCellValue('C14', "'(3)") 
        ->setCellValue('D14', "'(4)")
        ->setCellValue('E14', "'(5)")
        ->setCellValue('F14', "'(6)")
        ->setCellValue('K14', "'(7)")
        ->setCellValue('L14', "'(8)")
        ->setCellValue('M14', "'(9)");
    

    $sql = "SELECT a.ncredit, a.cewtcode, a.ctranno, b.ngross, b.dapvdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc FROM apv_t a
        LEFT JOIN apv b ON a.compcode = b.compcode AND a.ctranno = b.ctranno
        LEFT JOIN suppliers c ON a.compcode = b.compcode AND b.ccode = c.ccode 
        LEFT JOIN groupings d ON a.compcode = b.compcode AND c.csuppliertype = d.ccode
        WHERE a.compcode = '$company' AND MONTH(b.dapvdate) = '$month' AND YEAR(b.dapvdate) = '$year' AND  b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 AND d.ctype = 'SUPTYP'";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $index = 15;
        $TOTAL_GROSS =0;
        $TOTAL_CREDIT = 0;;
        while($row = $query -> fetch_array(MYSQLI_ASSOC)){
            
            $code = $row['cewtcode'];
            $credit = $row['ncredit'];
            if(strlen($code) != 0 && $credit != 0){
                $fullAddress = stringValidation($row['chouseno']);
                if(trim($row['ccity']) != ""){
                    $fullAddress .= " ". stringValidation($row['ccity']);
                }
                $ewt = getEWT($code);
                $gross = $row['ngross'];
                if($ewt['valid']) {
                    $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
                    $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue("A$index", $row['dapvdate'])
                    ->setCellValue("B$index", $row['ctranno'])
                    ->setCellValue("C$index", $row['ctin'])
                    ->setCellValue("D$index", $row['cname'])
                    ->setCellValue("E$index", $fullAddress)
                    ->setCellValue("F$index", $ewt['code'])
                    /**
                     * @param G$index value was unknown
                     */
                    ->setCellValue("G$index", "")
                    ->setCellValue("H$index", $gross)
                    ->setCellValue("I$index", number_format($ewt['rate'], 2))
                    ->setCellValue("J$index", $credit)
                    ->setCellValue("K$index", $gross)
                    ->setCellValue("L$index", $ewt['rate'])
                    ->setCellValue("M$index", $credit);

                    $TOTAL_GROSS += floatval($row['ngross']); 
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
	$spreadsheet->getActiveSheet()->setTitle('QUARTERLY ALPHALIST OF PAYEES');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="QAP-Q2 ' . $year . ' - ' . $month_text . '.xlsx"');
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
