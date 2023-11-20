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
$code = $_REQUEST['xlsVat'];

$sql = "SELECT * FROM company WHERE compcode='$company'";
$result = mysqli_query($con, $sql);
$comp = mysqli_fetch_array($result, MYSQLI_ASSOC);

$sql = "SELECT a.cacctno FROM accounts_default a WHERE a.compcode = '$company_code' AND a.ccode = 'PURCH_VAT' ORDER BY a.cacctno DESC LIMIT 1";
$query = mysqli_query($con, $sql);
$account = $query -> fetch_array(MYSQLI_ASSOC);
$vat_code = $account['cacctno'];

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Purchase Transaction')
    ->setSubject('Reconcilation of listing for Enforcement')
    ->setDescription('Reconcilation of listing for enforcement, generated using Myx Financials.')
    ->setKeywords('myx_financials Reconcilation of listing for enforcement')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:A9')->getFont()->setBold(true);

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 

    $spreadsheet->getActiveSheet()->getStyle('A11:N13')->getFont()->setBold(true);
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'PURCHASE TRANSACTION')
        ->setCellValue('A2', 'RECONCILIATION OF LISTING FOR ENFORCEMENT')
        ->setCellValue('A6', 'Vat Registered Tin: ' . TinValidation($comp['comptin']))
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
        ->setCellValue('J12', "PURCHASE SERVICE")
        ->setCellValue('K11', "AMOUNT OF")
        ->setCellValue('K12', "PURCHAHSE OF CAPITAL GOODS")
        ->setCellValue('L11', "AMOUNT OF")
        ->setCellValue('L12', "PURCHASE GOODS OTHER THAN CAPIAL GOODS")
        ->setCellValue('M11', "AMOUNT OF")
        ->setCellValue('M12', "OUTPUT TAX")
        ->setCellValue('N11', "AMOUNT OF")
        ->setCellValue('N12', "GROSS TAXABLE SALES");

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A14', trim("'(1)"))
        ->setCellValue('B14', trim("'(2)"))
        ->setCellValue('C14', trim("'(3)"))
        ->setCellValue('D14', trim("'(4)"))
        ->setCellValue('E14', trim("'(5)"))
        ->setCellValue('F14', trim("'(6)"))
        ->setCellValue('G14', trim("'(7)"))
        ->setCellValue('H14', trim("'(8)"))
        ->setCellValue('I14', trim("'(9)"))
        ->setCellValue('J14', trim("'(10)"))
        ->setCellValue('K14', trim("'(11)"))
        ->setCellValue('L14', trim("'(12)"))
        ->setCellValue('M14', trim("'(13)"))
        ->setCellValue('N14', trim("'(14)"));

    $sql = "SELECT a.*, b.* FROM paybill a
        LEFT JOIN suppliers b on a.compcode = b.compcode AND a.ccode = b.ccode
        WHERE a.compcode = '$company_code'
        AND MONTH(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $monthcut
        AND YEAR(STR_TO_DATE(a.dcheckdate, '%Y-%m-%d')) = $yearcut
        AND b.cvattype = '$code'
        AND ctranno in (
            SELECT a.ctranno FROM paybill_t a 
            LEFT JOIN apv_t b on a.compcode = b.compcode AND a.capvno = b.ctranno
            WHERE a.compcode = '$company_code' AND b.cacctno = '$vat_code'
        )
        AND a.lapproved = 1 AND (a.lcancelled != 1 OR a.lvoid != 1)";
    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $index = 14;
        $TOTAL_GROSS =0; $TOTAL_EXEMPT = 0; $TOTAL_ZERO_RATED = 0; $TOTAL_TAXABLE = 0; $TOTAL_VAT = 0; $TOTAl_TAX_GROSS = 0;
        while($row = $query -> fetch_array(MYSQLI_ASSOC)){
            $computation = ComputePaybills($row);
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
                $fullAddress .= " ". stringValidation( $row['czip']);
            }
            $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue("A$index", $row['dcheckdate'])
            ->setCellValue("B$index", TinValidation($row['ctin']))
            ->setCellValue("C$index", $row['cname'])
            ->setCellValue("E$index", $fullAddress)
            ->setCellValue("F$index", $computation['gross'])
            ->setCellValue("G$index", $computation['exempt'],2)
            ->setCellValue("H$index", $computation['zero'],2)
            ->setCellValue("I$index", $computation['net'],2)
            ->setCellValue("J$index", $computation['service'],2)
            ->setCellValue("K$index", $computation['capital'],2)
            ->setCellValue("L$index", $computation['goods'],2)
            ->setCellValue("M$index", $computation['vat'],2)
            ->setCellValue("N$index", $computation['gross_vat'],2);

            // $TOTAL_GROSS += floatval($computation['gross']); 
            // $TOTAL_EXEMPT += floatval($computation['exempt']); 
            // $TOTAL_ZERO_RATED += floatval($computation['zero']); 
            // $TOTAL_TAXABLE += floatval($computation['net']); 
            // $TOTAL_VAT += floatval($computation['vat']);
            // $TOTAl_TAX_GROSS += floatval($computation['gross_vat']);
        }
        $lastindex = $index;
        $index += 2;

        $spreadsheet->getActiveSheet()->getStyle("A$index:N$index")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("F$index:N$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index", "GRAND TOTAL")
        ->setCellValue("F$index", "=SUM(F15:F$lastindex)")
        ->setCellValue("G$index", "=SUM(G15:G$lastindex)")
        ->setCellValue("H$index", "=SUM(H15:H$lastindex)")
        ->setCellValue("I$index", "=SUM(I15:I$lastindex)")
        ->setCellValue("J$index", "=SUM(J15:J$lastindex)")
        ->setCellValue("K$index", "=SUM(K15:K$lastindex)")
        ->setCellValue("L$index", "=SUM(L15:L$lastindex)")
        ->setCellValue("M$index", "=SUM(M15:M$lastindex)")
        ->setCellValue("N$index", "=SUM(N15:N$lastindex)");

        $index += 2;
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","END OF REPORT");
    } else {
        $spreadsheet->setActiveSheetIndex(0)
        -> setCellValue("A15", "NO RECORD");
    }


	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Purchase Transaction');
    for ($column = 'A'; $column <= 'N'; $column++) {
        $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(150/7);
    }
   
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
