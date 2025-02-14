<?php 
if(!isset($_SESSION)){
    session_start();
}

$_SESSION['pageid'] = "PurchaseDat";

require_once  "../../vendor2/autoload.php";
require_once "../../Connection/connection_string.php";
include('../../include/denied.php');
include('../../include/access2.php');

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

$sql = "SELECT a.cacctno FROM accounts_default a WHERE a.compcode = '$company' AND a.ccode = 'PURCH_VAT' ORDER BY a.cacctno DESC LIMIT 1";
$query = mysqli_query($con, $sql);
$account = $query -> fetch_array(MYSQLI_ASSOC);
$vat_code = $account['cacctno'];

//getallapv with input tax
$allapvno = array();
$apventry = array();
$sql = "SELECT A.cmodule, A.ctranno, A.ctaxcode, B.nrate, A.ndebit, A.ncredit FROM glactivity A left join vatcode B on A.compcode=B.compcode and A.ctaxcode=B.cvatcode WHERE A.compcode = '$company' AND A.acctno = '$vat_code' and MONTH(A.ddate)=$monthcut and YEAR(A.ddate)=$yearcut";
$query = mysqli_query($con, $sql);
if(mysqli_num_rows($query) != 0){
    while($row = $query -> fetch_assoc()){
        $allapvno[] = $row['ctranno'];
        $apventry[$row['ctranno']] = $row;
    }
}

//getall apv with payment
/*$allapvpaid = array();
$sql = "SELECT A.ctranno, A.capvno FROM paybill_t A left join paybill B on A.compcode=B.compcode and A.ctranno=B.ctranno WHERE A.compcode = '$company' AND A.capvno in ('".implode("','",$allapvno)."') AND (B.lapproved = 1 AND B.lvoid = 0)";
$query = mysqli_query($con, $sql);
if(mysqli_num_rows($query) != 0){
    while($row = $query -> fetch_assoc()){
        $allapvpaid[] = $row['capvno'];
    }
}*/

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

        $index = 14;

        $sql = "SELECT A.ctranno, A.ngross, A.ccode, A.chouseno, A.ccity, A.cstate, A.ccountry, A.ctin, A.cname, A.dapvdate
        From
        (
        SELECT A.ctranno, A.ngross, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.dapvdate as dapvdate
        FROM apv A 
        left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
        WHERE A.compcode = '$company' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
        
        UNION ALL
        
        SELECT A.ctranno, A.ntotdebit as ngross, A.ccode, B.chouseno, B.ccity, B.cstate, B.ccountry, B.ctin, B.cname, A.djdate as dapvdate 
        FROM journal A 
        left join suppliers B on A.compcode=B.compcode and A.ccode=B.ccode 
        WHERE A.compcode = '$company' AND A.ctranno in ('".implode("','",$allapvno)."') AND (A.lapproved = 1 AND A.lvoid = 0) 
        ) A
        Order by A.dapvdate, A.cname";

        $query = mysqli_query($con, $sql);
        if(mysqli_num_rows($query) != 0){
            
            while($row = $query -> fetch_assoc()){

                $fullAddress = str_replace(",", "", $row['chouseno']);
                if(trim($row['ccity']) != ""){
                    $fullAddress .= " ". str_replace(",", "", $row['ccity']);
                }
                if(trim($row['cstate']) != ""){
                    $fullAddress .= " ". str_replace(",", "", $row['cstate']);
                }
                if(trim($row['ccountry']) != ""){
                    $fullAddress .= " ". str_replace(",", "", $row['ccountry']);
                }
            
                $xcnet = 0;
                $xcvat = 0;
                $xczerotot = 0;
                $xcexmpt = 0;
                $xservc = 0;
                $xsgoods = 0;
                $xsgoodsother = 0;
                $rowgros = 0;

                if($apventry[$row['ctranno']]['nrate']>0){
                   if(floatval($apventry[$row['ctranno']]['ndebit'])>0) {
                        $xcvbam = floatval($apventry[$row['ctranno']]['ndebit']);
                        $rowgros = $row['ngross'];
                    }else{
                        $xcvbam = 0-floatval($apventry[$row['ctranno']]['ncredit']);
                        $rowgros = 0-$row['ngross'];
                    }

                    $xcnet = $xcvbam / (floatval($apventry[$row['ctranno']]['nrate'])/100);
                    $xcvat = $xcvbam;

                    if($apventry[$row['ctranno']]['ctaxcode']=="VTSDOM" || $apventry[$row['ctranno']]['ctaxcode'] == "VTSNR"){
                        $xservc = $xcnet;
                    }

                    if($apventry[$row['ctranno']]['ctaxcode']=="VTGE1M" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGNE1M"){
                        $xsgoods = $xcnet;
                    }

                    if($apventry[$row['ctranno']]['ctaxcode']=="VTGIMOCG" || $apventry[$row['ctranno']]['ctaxcode'] == "VTGOCG"){
                        $xsgoodsother = $xcnet;
                    }
                }

                if($apventry[$row['ctranno']]['ctaxcode']=="VTZERO"){
                    $xczerotot = floatval($row['ngross']);
                }

                if($apventry[$row['ctranno']]['ctaxcode']=="VTNOTQ"){
                    $xcexmpt = floatval($row['ngross']);
                }

                /*$TOTAL_GROSS += floatval($row['ngross']);
                $TOTAL_NET += floatval($xcnet);
                $TOTAL_VAT += floatval($xcvat);
                $TOTAL_EXEMPT += floatval($xcexmpt);
                $TOTAL_ZERO_RATED += floatval($xczerotot);
                $TOTAL_GOODS += floatval($xsgoods);
                $TOTAL_SERVICE += floatval($xservc);
                $TOTAL_CAPITAL += floatval($xsgoodsother);
                $TOTAL_TAX_GROSS += floatval($xcnet) + floatval($xcvat);*/


                $index++;
                $spreadsheet->getActiveSheet()->getStyle("F$index:N$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue("A$index", $row['dapvdate'])
                ->setCellValue("B$index", TinValidation($row['ctin']))
                ->setCellValue("C$index", $row['cname'])
                ->setCellValue("E$index", $fullAddress)
                ->setCellValue("F$index",  round((float)$rowgros,2))
                ->setCellValue("G$index",  round((float)$xcexmpt,2))
                ->setCellValue("H$index",  round((float)$xczerotot,2))
                ->setCellValue("I$index",  round((float)$xcnet,2))
                ->setCellValue("J$index",  round((float)$xservc,2))
                ->setCellValue("K$index",  round((float)$xsgoods,2))
                ->setCellValue("L$index",  round((float)$xsgoodsother,2))
                ->setCellValue("M$index",  round((float)$xcvat,2))
                ->setCellValue("N$index",  round((float)(floatval($xcnet) + floatval($xcvat)),2));

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
	header('Content-Disposition: attachment;filename="Purchase_Relief.xlsx"');
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
