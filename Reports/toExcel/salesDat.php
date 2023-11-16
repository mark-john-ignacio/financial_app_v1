<?php 
if(!isset($_SESSION)){
    session_start();
}
require_once  "../../vendor2/autoload.php";
require_once "../../Connection/connection_string.php";
require_once "../../Model/helper.php";

//use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$company = $_SESSION['companyid'];
$monthcut = $_REQUEST['xlsmonth'];
$yearcut = $_REQUEST['xlsyear'];

$sql = "SELECT * FROM company WHERE compcode='$company'";
$result = mysqli_query($con, $sql);
$comp = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Sales Transaction')
    ->setSubject('Reconcilation of listing for Enforcement')
    ->setDescription('Reconcilation of listing for enforcement, generated using Myx Financials.')
    ->setKeywords('myx_financials Reconcilation of listing for enforcement')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

    $spreadsheet->getActiveSheet()->getStyle('A11:K13')->getFont()->setBold(true);
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'SALES TRANSACTION')
        ->setCellValue('A2', 'RECONCILIATION OF LISTING FOR ENFORCEMENT')
        ->setCellValue('A6', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A7', "OWNER'S NAME: " . $comp['compname'])
        ->setCellValue('A8', "OWNER'S TRADE NAME: " . $comp['compdesc'])
        ->setCellValue('A9', "OWNER'S ADDRESS: " . $comp['compadd']);


    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A11', "TAXABLE")
        ->setCellValue('A12', "MONTH")
        ->setCellValue('B11', "TAXPAYER")
        ->setCellValue('B12', "IDENTIFICATION")
        ->setCellValue('B13', "NUMBER")
        ->setCellValue('C11', "REGISTER")
        ->setCellValue('D11', "NAME OF CUSTOMER")
        ->setCellValue('D12', "(Last Name, First Name, Middle Name)")
        ->setCellValue('E11', "CUSTOMER'S ADDRESS")
        ->setCellValue('F11', "AMOUNT OF")
        ->setCellValue('F12', "GROSS SALES")
        ->setCellValue('G11', "AMOUNT OF")
        ->setCellValue('G12', "EXEMPT SALES")
        ->setCellValue('H11', "AMOUNT OF")
        ->setCellValue('H12', "ZERO RATED SALES")
        ->setCellValue('I11', "AMOUNT OF")
        ->setCellValue('I12', "TAXABLE SALES")
        ->setCellValue('J11', "AMOUNT OF")
        ->setCellValue('J12', "OUTPUT TAX")
        ->setCellValue('K11', "AMOUNT OF")
        ->setCellValue('K12', "GROSS TAXABLE SALES");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A14', "'(1)")
        ->setCellValue('B14', "'(2)")
        ->setCellValue('C14', "'(3)")
        ->setCellValue('D14', "'(4)")
        ->setCellValue('E14', "'(5)")
        ->setCellValue('F14', "'(6)")
        ->setCellValue('G14', "'(7)")
        ->setCellValue('H14', "'(8)")
        ->setCellValue('I14', "'(9)")
        ->setCellValue('J14', "'(10)")
        ->setCellValue('K14', "'(11)");

    

    $sql = "SELECT a.*, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company' 
    AND MONTH(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $monthcut 
    AND YEAR(STR_TO_DATE(a.dcutdate, '%Y-%m-%d')) = $yearcut  
    AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0
    AND a.ctranno in (
        SELECT b.csalesno FROM receipt a 
        left join receipt_sales_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                    WHERE a.compcode = '$company' 
                    -- AND b.ctaxcode <> 'NT'
                    AND a.lapproved = 1 
                    AND a.lvoid = 0 
                    AND a.lcancelled = 0
    )";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $index = 14;
        $TOTAL_GROSS =0; $TOTAL_EXEMPT = 0; $TOTAL_ZERO_RATED = 0; $TOTAL_TAXABLE = 0; $TOTAL_VAT = 0; $TOTAl_TAX_GROSS = 0;
        while($row = $query -> fetch_array(MYSQLI_ASSOC)){
            $computation = ComputeRST($row['ctranno']);
            $index++;
            $fullAddress = str_replace(",", "", $row['chouseno']);
            if(trim($row['ccity']) != ""){
                $fullAddress .= " ". str_replace(",", "", $row['ccity']);
            }
            if(trim($row['ccountry']) != ""){
                $fullAddress .= " ". str_replace(",", "", $row['ccountry']);
            }
            if(trim($row['cstate']) != ""){
                $fullAddress .= " ". str_replace(",", "", $row['cstate']);
            }
            
            if(trim($row['czip']) != ""){
                $fullAddress .= " ". str_replace(",", "", $row['czip']);
            }
            $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$index", $row['dcutdate'])
            ->setCellValue("B$index", strval($row['ctin']))
            ->setCellValue("C$index", $row['cname'])
            ->setCellValue("E$index", $fullAddress)
            ->setCellValue("F$index", $computation['gross'])
            ->setCellValue("G$index", $computation['exempt'],2)
            ->setCellValue("H$index", $computation['zero'],2)
            ->setCellValue("I$index", $computation['net'],2)
            ->setCellValue("J$index", $computation['vat'],2)
            ->setCellValue("K$index", $computation['gross_vat'],2);

            $TOTAL_GROSS += floatval($computation['gross']); 
            $TOTAL_EXEMPT += floatval($computation['exempt']); 
            $TOTAL_ZERO_RATED += floatval($computation['zero']); 
            $TOTAL_TAXABLE += floatval($computation['net']); 
            $TOTAL_VAT += floatval($computation['vat']);
            $TOTAl_TAX_GROSS += floatval($computation['gross_vat']);
        }
        $lastindex = $index;
        $index += 2;

        $spreadsheet->getActiveSheet()->getStyle("A$index:K$index")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","GRAND TOTAL")
        ->setCellValue("F$index", "=SUM(F15:F$lastindex)")
        ->setCellValue("G$index", "=SUM(G15:G$lastindex)")
        ->setCellValue("H$index", "=SUM(H15:H$lastindex)")
        ->setCellValue("I$index", "=SUM(I15:I$lastindex)")
        ->setCellValue("J$index", "=SUM(J15:J$lastindex)")
        ->setCellValue("K$index", "=SUM(K15:K$lastindex)");

        $index += 2;
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","END OF REPORT");
    } else {
        $spreadsheet->setActiveSheetIndex(0)
        -> setCellValue("A15", "NO RECORD");
    }


	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Sales Transaction');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Sales_Transaction.xlsx"');
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
