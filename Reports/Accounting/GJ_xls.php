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
    ->setTitle('General Journal Report')
    ->setSubject('General Journal Report')
    ->setDescription('General Journal Report, generated using Myx Financials.')
    ->setKeywords('myx_financials general_journal')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit, A.ddate, C.cmemo
			From glactivity A 
			left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
			left join journal C on A.compcode=C.compcode and A.ctranno=C.ctranno
			Where A.compcode='$company' and A.cmodule='JE' and A.ddate between STR_TO_DATE('".$_REQUEST['date1']."', '%m/%d/%Y') and STR_TO_DATE('".$_REQUEST['date2']."', '%m/%d/%Y')
			Order By A.ctranno, A.nidentity";

		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: General Journal ')
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
        $controller = CustomerNames($row['cmodule'], $ctranno, $company);

        $cntr++;

        $spreadsheet->setActiveSheetIndex(0) 
        ->setCellValue('A7', 'DATE')
        ->setCellValue('B7', 'REFERENCE')
        ->setCellValue('C7', 'DESCRIPTION')
        ->setCellValue('D7', 'CUSTOMER NAME')
        ->setCellValue('E7', 'ACCOUNT TITLE ')
        ->setCellValue('F7', 'DEBIT')
        ->setCellValue('G7', 'CREDIT');

        // $spreadsheet->getActiveSheet()
        // ->getStyle("A7:F7")->getFill()
        // ->setFillType(Fill::FILL_SOLID)
        // ->getStartColor()->setARGB('fcb103');
		$cnt++;
    
       
            
        $ntotdebit = $ntotdebit + floatval($row['ndebit']);
        $ntotcredit = $ntotcredit + floatval($row['ncredit']);

        $ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

        $ntotGBal = $ntotGBal + $ntotbal;

        $dresult = mysqli_query($con, $controller);
        $namerow = mysqli_fetch_array($dresult, MYSQLI_ASSOC);
        
        $name = @$namerow['cname'];
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$cnt, $row['ddate'])
            ->setCellValue('B'.$cnt, $row['ctranno'])
            ->setCellValue('C'.$cnt, $row['cmemo'])
            ->setCellValue('D'.$cnt, (@$namerow['cname'] != null ? @$namerow['cname'] : ''))
            ->setCellValue('E'.$cnt, $row['cacctdesc'])
            ->setCellValue('F'.$cnt, $row['ndebit'])
            ->setCellValue('G'.$cnt, $row['ncredit']);

            $spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
            $spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        
            $tranno = $row['ctranno'];
	}
    $cnt += 2;
    $spreadsheet->setActiveSheetIndex(0) 
            ->setCellValue('E'.$cnt, 'Total: ')
            ->setCellValue('F'.$cnt, floatval($ntotdebit))
            ->setCellValue('G'.$cnt, floatval($ntotcredit));
    $spreadsheet->getActiveSheet()->getStyle("A$cnt:G$cnt")->getFont()->setBold(true);

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('General Journal');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="General_Journal.xlsx"');
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