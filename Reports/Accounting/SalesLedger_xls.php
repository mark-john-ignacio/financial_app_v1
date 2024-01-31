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
    ->setTitle('Sales Book Report')
    ->setSubject('Sales Book Report')
    ->setDescription('Sales Book Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_book')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	$sql = "select A.dcutdate, A.csalesno, A.ccode, A.cname, A.acctno, A.ctitle, A.ncredit, A.ndebit, A.lcancelled, A.lapproved
        FROM(
        select a.dcutdate, a.ctranno as csalesno, a.ccode, c.cname, b.acctno, b.ctitle, b.ncredit, b.ndebit, a.lcancelled, a.lapproved
        From sales a
        left join glactivity b on a.ctranno=b.ctranno and a.compcode=b.compcode
        left join customers c on a.ccode=c.cempid and a.compcode=c.compcode
        where a.compcode='$company' and a.dcutdate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y')
        ) A
        order by A.dcutdate, A.csalesno, A.ndebit desc";
		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: SALES BOOK ')
        ->setCellValue('A5', "For the Month of $date1 to $date2");

	$ntotdebit = 0;
	$ntotcredit = 0;
	$cntr=0;
	$ntotbal = 0;
	$ntotGBal = 0;

	$cnt = 7;
    $acct = null;


    $salesno = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
    
    $tranno = "";
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
        if($salesno!=$row['csalesno']){
			$code = $row['ccode'];
			$name= $row['cname'];
			$invval = $row['csalesno'];
			$dateval= date_format(date_create($row['dcutdate']),"m/d/Y");
			$classcode="class='rpthead'";
		}

        $cntr++;

        $spreadsheet->setActiveSheetIndex(0) 
        ->setCellValue('A7', 'DATE')
        ->setCellValue('B7', 'REFERENCE')
        ->setCellValue('C7', 'SUPPLIER NAME')
        ->setCellValue('D7', 'ACCOUNT NO')
        ->setCellValue('E7', 'ACCOUNT TITLE ')
        ->setCellValue('F7', 'DEBIT')
        ->setCellValue('G7', 'CREDIT');

        // $spreadsheet->getActiveSheet()
        // ->getStyle("A7:F7")->getFill()
        // ->setFillType(Fill::FILL_SOLID)
        // ->getStartColor()->setARGB('fcb103');
		$cnt++;


        if($row['lcancelled']==1){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$cnt, $row['dcutdate'])
                ->setCellValue('B'.$cnt, $row['csalesno'])
                ->setCellValue('C'.$cnt, $row['cname'])
                ->setCellValue('D'.$cnt, $row['acctno'])
                ->setCellValue('E'.$cnt, $row['ctitle'])
                ->setCellValue("F$cnt", "Cancelled");
        } elseif($row['lapproved']==0){
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$cnt, $row['dcutdate'])
                ->setCellValue('B'.$cnt, $row['csalesno'])
                ->setCellValue('C'.$cnt, $row['cname'])
                ->setCellValue('D'.$cnt, $row['acctno'])
                ->setCellValue('E'.$cnt, $row['ctitle'])
                ->setCellValue("F$cnt", " Not Yet Posted");

        } else {        
            $ntotdebit = $ntotdebit + floatval($row['ndebit']);
            $ntotcredit = $ntotcredit + floatval($row['ncredit']);

            $ntotbal = floatval($row['ndebit']) - floatval($row['ncredit']);

            $ntotGBal = $ntotGBal + $ntotbal;

            
            $name = @$namerow['cname'];
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$cnt, $dateval)
                ->setCellValue('B'.$cnt, $row['csalesno'])
                ->setCellValue('C'.$cnt, $row['cname'])
                ->setCellValue('D'.$cnt, $row['acctno'])
                ->setCellValue('E'.$cnt, $row['ctitle'])
                ->setCellValue('F'.$cnt, $row['ndebit'])
                ->setCellValue('G'.$cnt, $row['ncredit']);

            $spreadsheet->setActiveSheetIndex(0)->getStyle('F'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
            $spreadsheet->setActiveSheetIndex(0)->getStyle('G'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        
            $code = "";
            $name= "";
            $invval = "";
            $dateval="";		
            $classcode="";		
            $salesno=$row['csalesno'];
            $ntotdebit=$row['ndebit']+$ntotdebit;	
            $ntotcredit=$row['ncredit']+$ntotcredit;
        }
       
	}
    $cnt += 2;
    $spreadsheet->setActiveSheetIndex(0) 
            ->setCellValue('E'.$cnt, 'Total: ')
            ->setCellValue('F'.$cnt, floatval($ntotdebit))
            ->setCellValue('G'.$cnt, floatval($ntotcredit));
    $spreadsheet->getActiveSheet()->getStyle("A$cnt:F$cnt")->getFont()->setBold(true);

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

?>