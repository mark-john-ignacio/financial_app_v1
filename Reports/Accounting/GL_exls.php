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
    ->setTitle('General Ledger Report')
    ->setSubject('General Ledger Report')
    ->setDescription('General Ledger Report, generated using Myx Financials.')
    ->setKeywords('myx_financials general_ledger')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);


    $jqy = "";
	if($_REQUEST['selbanks']!=""){
		$jqy = " and A.acctno = '".$_POST['selbanks']."' ";
	}

	$sql = "Select A.cmodule, A.ctranno, A.ddate, A.acctno, B.cacctdesc, A.ndebit, A.ncredit, A.crefno
	From glactivity A left join accounts B on A.compcode=B.compcode and A.acctno=B.cacctid
	Where A.compcode='$company' and A.ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')".$jqy."
	Order By A.acctno, A.dpostdate, A.ctranno, CASE WHEN (A.ndebit <> 0) THEN 1 ELSE 0 END desc, A.acctno";

		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: General Ledger ')
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
        $ctranno = $row['ctranno'];
        $controller = CustomerNames($row['cmodule'], $ctranno, $company, $row['crefno']);
        $DescCont = ReadDescription($row['cmodule'], $ctranno, $company);


        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', 'Date')
        ->setCellValue('B7', 'Reference')
        ->setCellValue('C7', 'Description')
        ->setCellValue('D7', 'Customer/Supplier')
        ->setCellValue('E7', 'Account Code')
        ->setCellValue('F7', 'Account Title')
        ->setCellValue('G7', 'Debit')
        ->setCellValue('H7', 'Credit')
        ->setCellValue('I7', 'Balance');

        // $spreadsheet->getActiveSheet()
        // ->getStyle("A7:G7")->getFill()
        // ->setFillType(Fill::FILL_SOLID)
        // ->getStartColor()->setARGB('fcb103');
        $cnt++;
    
        $dresult = mysqli_query($con, $controller);
        $namerow = mysqli_fetch_array($dresult, MYSQLI_ASSOC);

        $desconres = mysqli_query($con, $DescCont);
        $descrow = mysqli_fetch_array($desconres, MYSQLI_ASSOC);
       
            
        $ntotdebit = $ntotdebit + floatval($row['ndebit']);
        $ntotcredit = $ntotcredit + floatval($row['ncredit']);

        $ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

        $ntotGBal = $ntotGBal + $ntotbal;

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A'.$cnt, $row['ddate'])
            ->setCellValue('B'.$cnt, $row['ctranno'])
            ->setCellValue('C'.$cnt, (@$descrow['typ'] != null ? @$descrow['typ'] : ''))
            ->setCellValue('D'.$cnt, (@$namerow['cname'] != null ? @$namerow['cname'] : ''))
            ->setCellValue('E'.$cnt, $row['acctno'])
            ->setCellValue('F'.$cnt, $row['cacctdesc'])
            ->setCellValue('G'.$cnt, $row['ndebit'])
            ->setCellValue('H'.$cnt, $row['ncredit'])
            ->setCellValue('I'.$cnt, $ntotbal);

            $spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
            $spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
            $spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        
	}
    $cnt += 2;

    $ntotGBalTB = floatval($ntotdebit) - floatval($ntotcredit);
    $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('F'.$cnt, 'Total')
            ->setCellValue('G'.$cnt, floatval($ntotdebit))
            ->setCellValue('H'.$cnt, floatval($ntotcredit))
            ->setCellValue('I'.$cnt, floatval($ntotGBalTB));

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('General Ledger');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="General_Ledger.xlsx"');
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