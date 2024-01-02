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
    ->setTitle('Sales Register Report')
    ->setSubject('Sales Register Report')
    ->setDescription('Sales Register Report, generated using Myx Financials.')
    ->setKeywords('myx_financials sales_register')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

	$sql = "Select A.* From
        (
        SELECT 1 as orderd, A.`acctno`, A.`ctitle`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
        From `glactivity` A left join `sales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
        Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and B.`lvoid`= 0 and A.`ndebit` <> 0
        group by A.`acctno`, A.`ctitle`
        
        UNION ALL
        
        SELECT 2 as orderd, B.`ccode`, C.`cname`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
        From `glactivity` A left join `sales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
        left join `customers` C on B.`ccode`=C.`cempid` and B.`compcode`=C.`compcode`
        Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and B.`lvoid`= 0 and A.`ncredit` <> 0
        group by  B.`ccode`, C.`cname`
        
        UNION ALL
        
        SELECT 3 as orderd, A.`acctno`, A.`ctitle`, Sum(A.`ndebit`) as ndebit, Sum(A.`ncredit`) as ncredit
        From `glactivity` A left join `sales` B on A.`ctranno`=B.`ctranno` and A.`compcode`=B.`compcode`
        Where A.compcode='$company' and B.`dcutdate` between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and B.`lcancelled`=0 and B.`lapproved`=1 and B.`lvoid`= 0 and A.`ncredit` <> 0
        group by  A.`acctno`, A.`ctitle`
        ) A
        order by A.orderd, A.`acctno`";
		//	echo $sql;

	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: Sales Register ')
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
        if($row['orderd'] == 2){
            /**
             * data is dedicated for union 2
             */
            $acctno = "";
            $title = $row["acctno"]." - ".$row["ctitle"];
        } else {
            $acctno = $row["acctno"];
			$title = $row["ctitle"];
        }

        $cntr++;
        $cnt++;
        if($row["orderd"]==3 and $cntrtotal == 0){

            /**
             * data is dedicated for union 3 and count rate total is 0
             */
            $cntrtotal = 1;
            $spreadsheet->setActiveSheetIndex(0) 
                    ->setCellValue('B'.$cnt, 'Total: ')
                    ->setCellValue('C'.$cnt, floatval($ntotdebit))
                    ->setCellValue('D'.$cnt, floatval($ntotcredit));
            $spreadsheet->getActiveSheet()->getStyle("A$cnt:F$cnt")->getFont()->setBold(true);

            $ntotdebit  = 0;
			$ntotcredit  = 0;
            $cnt++;
        }

        $spreadsheet->setActiveSheetIndex(0) 
        ->setCellValue('A7', 'ACCOUNT NO')
        ->setCellValue('B7', 'ACCOUNT TITLE')
        ->setCellValue('C7', 'DEBIT')
        ->setCellValue('D7', 'CREDIT');

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
            ->setCellValue('A'.$cnt, $row['acctno'])
            ->setCellValue('B'.$cnt, $row['ctitle'])
            ->setCellValue('C'.$cnt, $row['ndebit'])
            ->setCellValue('D'.$cnt, $row['ncredit']);

        $spreadsheet->setActiveSheetIndex(0)->getStyle('C'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
        $spreadsheet->setActiveSheetIndex(0)->getStyle('D'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
	}
    $cnt += 2;
    $spreadsheet->setActiveSheetIndex(0) 
            ->setCellValue('B'.$cnt, 'Total: ')
            ->setCellValue('C'.$cnt, floatval($ntotdebit))
            ->setCellValue('D'.$cnt, floatval($ntotcredit));
    $spreadsheet->getActiveSheet()->getStyle("A$cnt:F$cnt")->getFont()->setBold(true);

	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Sales Register');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Sales_Register.xlsx"');
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