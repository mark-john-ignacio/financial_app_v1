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


$year = date("Y", strtotime($_POST['years']));
$quartersAndMonths = getQuartersAndMonths($year);


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
        ->setCellValue('A3', "FOR THE QUARTER ENDING $month_text, $year")
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

        ->setCellValue('N10', "2ND MONTH OF THE QUARTER")
        ->setCellValue('N11', "AMOUNT OF")
        ->setCellValue('N12', "INCOME PAYMENT")
        ->setCellValue('O11', "TAX RATE")
        ->setCellValue('P11', "AMOUNT OF")
        ->setCellValue('P12', "TAX WITHHELD")

        ->setCellValue('Q10', "3RD MONTH OF THE QUARTER")
        ->setCellValue('Q11', "AMOUNT OF")
        ->setCellValue('Q12', "INCOME PAYMENT")
        ->setCellValue('R11', "TAX RATE")
        ->setCellValue('S11', "AMOUNT OF")
        ->setCellValue('S12', "TAX WITHHELD")

        
        ->setCellValue('T10', "TOTAL FOR THE QUARTER")
        ->setCellValue('T11', "TOTAL")
        ->setCellValue('T12', "INCOME PAYMENT")
        ->setCellValue('U11', "TOTAL")
        ->setCellValue('U12', "TAX WITHHELD")
        
        ->setCellValue('A14', "'(1)")
        ->setCellValue('B14', "'(2)")
        ->setCellValue('C14', "'(3)") 
        ->setCellValue('D14', "'(4)")
        ->setCellValue('E14', "'(5)")
        ->setCellValue('K14', "'(6)")
        ->setCellValue('L14', "'(7)")
        ->setCellValue('M14', "'(8)")
        ->setCellValue('N14', "'(9)")
        ->setCellValue('O14', "'(10)")
        ->setCellValue('P14', "'(11)")
        ->setCellValue('Q14', "'(12)")
        ->setCellValue('R14', "'(13)")
        ->setCellValue('S14', "'(14)")
        ->setCellValue('T14', "'(15)")
        ->setCellValue('U14', "'(16)");
    
    

    $index = 15;
    $TOTAL_GROSS = 0;
    $TOTAL_CREDIT = 0;
    $count = 1;
    
    foreach ($quartersAndMonths as $quarter => $month) {
        $QUARTERDATA = dataquarterly($month);
    
        // var_dump($QUARTERDATA);
        if ($QUARTERDATA['valid']) {
            foreach($QUARTERDATA['quarter'] as $row) {
                $list = $row['data'];
                // var_dump($list);
                $code = $list['cewtcode'];
                $credit = $list['ncredit'];
                $gross = $list['ngross'];
    
                $ewt = getEWT($code);
                if (ValidateEWT($code) && $credit != 0 && $ewt['valid']) {
                    $fullAddress = stringValidation($list['chouseno']);
                    if (trim($list['ccity']) != "") {
                        $fullAddress .= " " . stringValidation($list['ccity']);
                    }
                    
                    $spreadsheet->getActiveSheet()->getStyle("T$index:U$index")->getFont()->setBold(true);
                    $spreadsheet->getActiveSheet()->getStyle("F$index:U$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue("A$index", $count)
                        ->setCellValue("B$index", $list['ctranno'])
                        ->setCellValue("C$index", $list['ctin'])
                        ->setCellValue("D$index", $list['cname'])
                        ->setCellValue("E$index", $fullAddress)
                        ->setCellValue("F$index", $ewt['code'])
                        ->setCellValue("G$index", "") // G$index value was unknown
                        ->setCellValue("H$index", $gross)
                        ->setCellValue("I$index", number_format($ewt['rate'], 2))
                        ->setCellValue("J$index", $credit)

                        ->setCellValue("K$index", $row['label'] === "First" ? $gross : 0)
                        ->setCellValue("L$index", $row['label'] === "First" ? $ewt['rate'] : 0)
                        ->setCellValue("M$index", $row['label'] === "First" ? $credit : 0)
            
                        ->setCellValue("N$index", $row['label'] === "Second" ? $gross : 0)
                        ->setCellValue("O$index", $row['label'] === "Second" ? $ewt['rate'] : 0)
                        ->setCellValue("P$index", $row['label'] === "Second" ? $credit : 0)
            
                        ->setCellValue("Q$index", $row['label'] === "Third" ? $gross : 0)
                        ->setCellValue("R$index", $row['label'] === "Third" ? $ewt['rate'] : 0)
                        ->setCellValue("S$index", $row['label'] === "Third" ? $credit : 0)
                        
                        ->setCellValue("T$index", "=K$index+N$index+Q$index")
                        ->setCellValue("U$index", "=M$index+P$index+S$index");


                    $TOTAL_GROSS += floatval($list['ngross']);
                    $TOTAL_CREDIT += floatval($credit);

                    $index++;
                    $count++;
                }
                
            }
        
            $index += 2;
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A$index", "END OF REPORT");
                
        } 
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
