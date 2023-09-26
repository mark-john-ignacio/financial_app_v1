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
$date1 = $_POST["date1"];
$month = date('F', strtotime($date1));
$year = date('Y', strtotime($date1));
$date2 = $_POST["date2"];

$sql = "SELECT * FROM company WHERE compcode='$company'";
$result = mysqli_query($con, $sql);
$comp = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Cash Disbursement Book Report')
    ->setSubject('Cash Disbursement Book Report')
    ->setDescription('Cash Disbursement Book Report, generated using Myx Financials.')
    ->setKeywords('myx_financials cash_disbursement')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

    $sql = "Select a.cmodule, b.ctranno, b.ccode, b.cpayee, b.ccheckno, a.acctno, a.ctitle, a.ndebit, a.ncredit, b.dcheckdate, b.cpayrefno, b.cpaymethod, c.ctin
	From glactivity a
	left join paybill b on a.compcode=b.compcode and a.ctranno=b.ctranno
	left join suppliers c on a.compcode=c.compcode and b.ccode = c.ccode
	where a.compcode='$company' and a.cmodule='PV' and b.dcheckdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
	order by b.ctranno, a.ndebit DESC";
    
		//	echo $sql;
	$result=mysqli_query($con,$sql);
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: Cash Disbursement Book ')
        ->setCellValue('A5', "For the Month of $date1 to $date2");

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$cnt = 7;
    $acct = null;

    
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
        foreach($row as $key => $value){
            echo $key;
        }
        $ctranno = $row['ctranno'];

        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', 'DATE')
        ->setCellValue('B7', 'TRANSACTION NO.')
        ->setCellValue('C7', 'REFERENCE NO.')
        ->setCellValue('D7', 'Customer NAME')
        ->setCellValue('E7', 'ACCOUNT NO.')
        ->setCellValue('F7', 'ACCOUNT TITLE')
        ->setCellValue('G7', 'DEBIT')
        ->setCellValue('H7', 'CREDIT');

        // $spreadsheet->getActiveSheet()
        // ->getStyle("A7:H7")->getFill()
        // ->setFillType(Fill::FILL_SOLID)
        // ->getStartColor()->setARGB('fcb103');
		// $cnt++;
       
        $ntotdebit = $ntotdebit + floatval($row['ndebit']);
        $ntotcredit = $ntotcredit + floatval($row['ncredit']);

        $ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

        $ntotGBal = $ntotGBal + $ntotbal;

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$cnt, $row['dcheckdate'])
            ->setCellValue('B'.$cnt, $row['ctranno'])
            ->setCellValue('C'.$cnt, $row['cpayrefno'])
            ->setCellValue('D'.$cnt, ($row['cpayee'] != null ? $row['cpayee'] : ''))
            ->setCellValue('E'.$cnt, $row['acctno'])
            ->setCellValue('F'.$cnt, $row['ctitle'])
            ->setCellValue('G'.$cnt, $row['ndebit'])
            ->setCellValue('H'.$cnt, $row['ncredit']);

            $spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
            $spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        
	}
    $cnt += 2;
    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('F'.$cnt, 'Total')
            ->setCellValue('G'.$cnt, $ntotdebit)
            ->setCellValue('H'.$cnt, $ntotcredit);

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Cash Disbursement Book');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="cash_disbursment_book.xlsx"');
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