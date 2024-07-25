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
$dmonth = date('F', strtotime($date2));
$dyear = date('Y', strtotime($date2));

$sql = "SELECT * FROM company WHERE compcode='$company'";
$result = mysqli_query($con, $sql);
$comp = mysqli_fetch_array($result, MYSQLI_ASSOC);

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Accounts Payable Jorunal Report')
    ->setSubject('Accounts Payable Jorunal Report')
    ->setDescription('Accounts Payable Jorunal Report, generated using Myx Financials.')
    ->setKeywords('myx_financials accounts_payable_journal')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	$sql = "Select b.ctranno, b.ccode, b.cpayee as cname, b.cpaymentfor, a.cacctno, a.ctitle, a.ndebit, a.ncredit, b.dapvdate, b.lapproved
	From apv_t a 
	left join apv b on a.compcode=b.compcode and a.ctranno=b.ctranno
	where a.compcode='$company' and b.dapvdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and b.lcancelled = 0 and b.lvoid = 0 and (a.ncredit<>0 or a.ndebit<>0)
	order by b.ctranno";
		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: Accounts Payable Journal ')
        ->setCellValue('A5', "For the Month of $date1 to $date2");

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$cnt = 7;
    $acct = null;
    
    $tranno = "";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
        $ctranno = $row['ctranno'];

        $cntr++;

        $spreadsheet->setActiveSheetIndex(0) 
        ->setCellValue('A7', 'DATE')
        ->setCellValue('B7', 'REFERENCE')
        ->setCellValue('C7', 'PARTICULARS')
        ->setCellValue('D7', 'CUSTOMER NAME')
        ->setCellValue('E7', 'ACCOUNT NO')
        ->setCellValue('F7', 'ACCOUNT TITLE ')
        ->setCellValue('G7', 'DEBIT')
        ->setCellValue('H7', 'CREDIT');

        // $spreadsheet->getActiveSheet()
        // ->getStyle("A7:F7")->getFill()
        // ->setFillType(Fill::FILL_SOLID)
        // ->getStartColor()->setARGB('fcb103');
		$cnt++;
    
       
            
        $ntotdebit = $ntotdebit + floatval($row['ndebit']);
        $ntotcredit = $ntotcredit + floatval($row['ncredit']);

        $ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

        $ntotGBal = $ntotGBal + $ntotbal;

        
        $name = @$namerow['cname'];
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$cnt, $row['dapvdate'])
            ->setCellValue('B'.$cnt, $row['ctranno'])
            ->setCellValue('C'.$cnt, $row['cpaymentfor'])
            ->setCellValue('D'.$cnt, $row['cname'])
            ->setCellValue('E'.$cnt, $row['cacctno'])
            ->setCellValue('F'.$cnt, $row['ctitle'])
            ->setCellValue('G'.$cnt, $row['ndebit'])
            ->setCellValue('H'.$cnt, $row['ncredit'])
            ->setCellValue('I'.$cnt, ($row['lapproved'] == 0 ? 'Unposted' : ''));

            $spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
            $spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        
            $tranno = $row['ctranno'];
	}
    $cnt += 2;
    $spreadsheet->setActiveSheetIndex(0) 
            ->setCellValue('F'.$cnt, 'Total: ')
            ->setCellValue('G'.$cnt, floatval($ntotdebit))
            ->setCellValue('H'.$cnt, floatval($ntotcredit));
    $spreadsheet->getActiveSheet()->getStyle("A$cnt:H$cnt")->getFont()->setBold(true);

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Accounts Payable Jorunal ');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Accounts_Payable_Journal.xlsx"');
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