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
    ->setTitle('Sales Recap Per Customer Report')
    ->setSubject('Sales Recap Per Customer Report')
    ->setDescription('Sales Recap Per Customer Report, generated using Myx Financials.')
    ->setKeywords('myx_financials recap_per_customer')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	$sql = "select  A.ccode, A.ddate, A.cname, A.acctno, A.ctitle, Sum(A.ncredit) as ncredit, Sum(A.ndebit) as ndebit
        FROM
        (
        select  a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.ddate
        From sales a
        left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
        left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
        where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
        and a.lapproved=1 and a.lvoid = 0
        ) A
        group by A.ccode, A.cname, A.acctno, A.ctitle
        order by A.ccode, sum(A.ndebit) desc";
		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: Sales Recap Per Customer')
        ->setCellValue('A5', "For the Month of $date1 to $date2");

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$cnt = 7;
    $acct = null;

    $acctno="";
	$title="";
	$cntrtotal  = 0;
    $tranno = "";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{

        $cntr++;
        $cnt++;

        $spreadsheet->setActiveSheetIndex(0) 
        ->setCellValue('A7', 'DATE')
        ->setCellValue('B7', 'CUSTOMER NAME')
        ->setCellValue('C7', 'ACCOUNT NO')
        ->setCellValue('D7', 'ACCOUNT TITLE')
        ->setCellValue('E7', 'DEBIT')
        ->setCellValue('F7', 'CREDIT');

        // $spreadsheet->getActiveSheet()
        // ->getStyle("A7:F7")->getFill()
        // ->setFillType(Fill::FILL_SOLID)
        // ->getStartColor()->setARGB('fcb103');
		
     
        $ntotdebit = $ntotdebit + floatval($row['ndebit']);
        $ntotcredit = $ntotcredit + floatval($row['ncredit']);

        $ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

        $ntotGBal = $ntotGBal + $ntotbal;
        
        $name = @$namerow['cname'];
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$cnt, $row['ddate'])
            ->setCellValue('B'.$cnt, $row['cname'])
            ->setCellValue('C'.$cnt, $row['acctno'])
            ->setCellValue('D'.$cnt, $row['ctitle'])
            ->setCellValue('E'.$cnt, $row['ndebit'])
            ->setCellValue('F'.$cnt, $row['ncredit']);

        $spreadsheet->setActiveSheetIndex(0)->getStyle('E'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        $spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	}
    $cnt += 2;
    $spreadsheet->setActiveSheetIndex(0) 
            ->setCellValue('D'.$cnt, 'Total: ')
            ->setCellValue('E'.$cnt, floatval($ntotdebit))
            ->setCellValue('F'.$cnt, floatval($ntotcredit));
    $spreadsheet->getActiveSheet()->getStyle("A$cnt:F$cnt")->getFont()->setBold(true);

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Sales Recap Per Customer');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Recap_Per_Customer.xlsx"');
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