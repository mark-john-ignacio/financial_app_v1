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

$date1 = $_REQUEST["date1"];
$date2 = $_REQUEST['date2'];

$sql = "SELECT * FROM company WHERE compcode='$company'";
$result = mysqli_query($con, $sql);
$comp = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Sales Journal')
    ->setSubject('Sales Journal Report')
    ->setDescription('Sales Journal, generated using Myx Financials.')
    ->setKeywords('myx_financials Sales Journal')
    ->setCategory('Myx Financials Report');

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 


    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Company: ' . $comp['compname'])
        ->setCellValue('A2', 'Company Address: ' . $comp['compadd'])
        ->setCellValue('A3', "Vat Registered Tin: " . $comp['comptin'])
        ->setCellValue('A4', "Kind of Book: SALES JOURNAL BOOK")
        ->setCellValue('A5', "For the Period " . date_format(date_create($date1),"F d, Y") . " to " . date_format(date_create($date2),"F d, Y"));

    $spreadsheet->getActiveSheet()->getStyle('A7:J7')->getFont()->setBold(true);
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', "Date")
        ->setCellValue('B7', "Customer's TIN")
        ->setCellValue('C7', "Customer's Name")
        ->setCellValue('D7', "Address")
        ->setCellValue('E7', "Description")
        ->setCellValue('F7', "Sales Invoice No.")
        ->setCellValue('G7', "Amount")
        ->setCellValue('H7', "Discount")
        ->setCellValue('I7', "VAT Amount")
        ->setCellValue('J7', "Net Sales");


    $sql = "SELECT a.*, b.cname, b.ctradename, b.ctin, b.chouseno, b.cstate, b.ccity, b.ccountry, b.czip, b.cvattype 
    FROM sales a 
    LEFT JOIN customers b on a.compcode = b.compcode AND a.ccode = b.cempid
    WHERE a.compcode = '$company' 
    AND a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')  
    AND a.lapproved = 1 AND a.lvoid = 0 AND a.lcancelled = 0
    AND a.ctranno in (
        SELECT b.csalesno FROM receipt a 
        left join receipt_sales_t b on a.compcode = b.compcode AND a.ctranno = b.ctranno
                    WHERE a.compcode = '$company' 
                    AND a.lapproved = 1 
                    AND a.lvoid = 0 
                    AND a.lcancelled = 0
    )";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $index = 7;
        $TOTAL_GROSS = 0;
        $TOTAL_NET = 0;
        $TOTAL_VAT = 0;
        $TOTAL_EXEMPT = 0;
        $TOTAL_ZERO_RATED = 0;
        $TOTAL_TAX_GROSS = 0;
        $TOTAL_DISCS = 0;

        while($row = $query -> fetch_array(MYSQLI_ASSOC)){
            $computation = ComputeRST($row);
            $index++;
            $fullAddress = stringValidation($row['chouseno']);
            if(trim($row['ccity']) != ""){
                $fullAddress .= " ". stringValidation($row['ccity']);
            }
            if(trim($row['ccountry']) != ""){
                $fullAddress .= " ". stringValidation($row['ccountry']);
            }
            if(trim($row['cstate']) != ""){
                $fullAddress .= " ". stringValidation($row['cstate']);
            }
            
            if(trim($row['czip']) != ""){
                $fullAddress .= " ". stringValidation($row['czip']);
            }

            $xzdisc = (floatval($computation['vat'])==0) ? $computation['gross'] : $computation['net'];

            $spreadsheet->getActiveSheet()->getStyle("G$index:J$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$index", $row['dcutdate'])
            ->setCellValue("B$index", TinValidation($row['ctin']))
            ->setCellValue("C$index", $row['cname'])
            ->setCellValue("D$index", $fullAddress)
            ->setCellValue("E$index", $row['cremarks'])
            ->setCellValue("F$index", $row['csiprintno'])
            ->setCellValue("G$index", $computation['gross'])
            ->setCellValue("H$index", $computation['total_discount'])
            ->setCellValue("I$index", $computation['vat'])
            ->setCellValue("J$index", $xzdisc);

            $TOTAL_GROSS += floatval($computation['gross']); 
            $TOTAL_TAXABLE += (floatval($computation['vat'])==0) ? floatval($computation['gross']) : floatval($computation['net']); 
           // $TOTAL_TAXABLE += floatval($computation['net']); 
            $TOTAL_VAT += floatval($computation['vat']);
            $TOTAL_DISCS += floatval($computation['total_discount']);
        }
        $lastindex = $index;
        $index += 2;

        $spreadsheet->getActiveSheet()->getStyle("A$index:J$index")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("G$index:J$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","GRAND TOTAL")
        ->setCellValue("G$index", "=SUM(G8:G$lastindex)")
        ->setCellValue("H$index", "=SUM(H8:H$lastindex)")
        ->setCellValue("I$index", "=SUM(I8:I$lastindex)")
        ->setCellValue("J$index", "=SUM(J8:J$lastindex)");

        $index += 2;
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","END OF REPORT");
    } else {
        $spreadsheet->setActiveSheetIndex(0)
        -> setCellValue("A7", "NO RECORD");
    }


	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Sales Journal');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Sales_Journal.xlsx"');
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
