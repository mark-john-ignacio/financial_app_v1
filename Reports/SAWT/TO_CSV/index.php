<?php 
    if(!isset($_SESSION)){
        session_start();
    }
    
    $_SESSION['pageid'] = "BIRSAWT";

    require_once "../../../vendor2/autoload.php";
    require_once "../../../Connection/connection_string.php";
    require_once "../../../include/denied.php";
    require_once "../../../include/access.php";
    require_once "../../../Model/helper.php";

    //use PhpOffice\PhpSpreadsheet\Helper\Sample;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use \PhpOffice\PhpSpreadsheet\Cell\DataType;
    use PhpOffice\PhpSpreadsheet\Style\Fill;

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $company = $_SESSION['companyid'];
    // $month = $_POST['months'];
    // $year = $_POST['years'];

    //$getEWT = $_POST['months'];
    //$month = date("m", strtotime($_POST['months']));
    $year = $_POST['years'];
    switch($_POST['selqrtr']){
        case 1:
            $months = "1,2,3";
            $month = 3;
            break;
        case 2:
            $months = "4,5,6";
            $month = 6;
            break;
        case 3:
            $months = "7,8,9";
            $month = 9;
            break;
        case 4:
            $months = "10,11,12";
            $month = 12;
            break;
        default: 
            $months = "";
            break;
    }

    $dateObj   = DateTime::createFromFormat('!m', $month);
    $month_text = $dateObj->format('F');

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('SAWT')
    ->setSubject('SAWT')
    ->setDescription('SAWT, generated using Myx Financials.')
    ->setKeywords('myx_financials SAWT')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

    $sql = "SELECT * FROM company WHERE compcode = '$company'";
	$query=mysqli_query($con,$sql);
	$comp = $query -> fetch_assoc();
    /**
     * Company Details
     */
    $spreadsheet->getActiveSheet()->getStyle('A11:K11')->getFont()->setBold(true);
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'BIR FORM 1702Q')
        ->setCellValue('A2', "SUMMARY ALPHALIST OF WITHHOLDING TAXES (SAWT)")
        ->setCellValue('A3', "FOR THE QUARTER ENDING $month_text, $year")
        ->setCellValue('A6', 'TIN: ' . TinValidation($comp['comptin']))
        ->setCellValue('A7', "PAYEE'S NAME: " . $comp['compname']);

    /**
     * List of Details
     */

    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A11', "SEQ")
        ->setCellValue('A12', "NO")
        ->setCellValue('B11', "TAXPAYER")
        ->setCellValue('B12', "IDENTIFICATION")
        ->setCellValue('B13', "NUMBER")
        ->setCellValue('C11', "CORPORATION")    
        ->setCellValue('C12', "(Registered Name)")
        ->setCellValue('D11', "INDIVIDUAL")
        ->setCellValue('D12', "(Last Name, First Name, Middle Name)")
        ->setCellValue('E11', "ATC CODE")
        ->setCellValue('F11', "NATURE OF PAYMENT")
        ->setCellValue('G11', "AMOUNT OF")
        ->setCellValue('G12', "INCOME PAYMENT")
        ->setCellValue('H11', "TAX RATE")
        ->setCellValue('I11', "AMOUNT OF ")
        ->setCellValue('I12', "TAX WITHHELD")

        
        ->setCellValue('A14', "'(1)")
        ->setCellValue('B14', "'(2)")
        ->setCellValue('C14', "'(3)") 
        ->setCellValue('D14', "'(4)")
        ->setCellValue('E14', "'(5)")
        ->setCellValue('F14', "'(6)")
        ->setCellValue('G14', "'(7)")
        ->setCellValue('H14', "'(8)")
        ->setCellValue('I14', "'(9)")
        
        ->setCellValue('A15', "'------------------------------")
        ->setCellValue('B15', "'------------------------------")
        ->setCellValue('C15', "'------------------------------") 
        ->setCellValue('D15', "'------------------------------")
        ->setCellValue('E15', "'------------------------------")
        ->setCellValue('F15', "'------------------------------")
        ->setCellValue('G15', "'------------------------------")
        ->setCellValue('H15', "'------------------------------")
        ->setCellValue('I15', "'------------------------------");
    

    $sql = "SELECT b.cewtcode, b.ctranno, b.ngrossbefore as ngross, b.dcutdate, c.cname, c.chouseno, c.ccity, c.ctin, d.cdesc 
    FROM sales b
    LEFT JOIN customers c on b.compcode = c.compcode AND b.ccode = c.cempid
    LEFT JOIN groupings d on c.compcode = d.compcode AND c.ccustomertype = d.ccode AND d.ctype = 'CUSTYP'
    WHERE b.compcode = '$company' AND MONTH(b.dcutdate) in ($months) AND YEAR(b.dcutdate) = '$year' AND b.lapproved = 1 AND b.lvoid = 0 AND b.lcancelled = 0 and IFNULL(b.cewtcode,'') <> '' Order By b.dcutdate, b.ctranno";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0){
        $index = 16;
        $TOTAL_GROSS =0; $TOTAL_CREDIT = 0;;
        while($row = $query -> fetch_array(MYSQLI_ASSOC)){
            
            $code = $row['cewtcode'];
            $gross = $row['ngross'];
            $ewt = getEWT($code);
            $rate = $ewt['valid'] ? $ewt['rate'] : 0;
            $toEwtAmt = $gross * ($rate / 100);
            $credit = round($toEwtAmt, 2);
            $ewtCode = $ewt['valid'] ? $ewt['code'] : "";

            if (ValidateEWT($code) && $ewt['valid']) {
                $fullAddress = stringValidation($row['chouseno']);
                if(trim($row['ccity']) != ""){
                    $fullAddress .= " ". stringValidation($row['ccity']);
                }
                $CORPORATE = "";
                $INDIVIDUAl = "";
                switch($row['cdesc']) {
                    case "PERSON": 
                        $INDIVIDUAl = stringValidation($row['cname']);
                        break;
                    case "COMPANY": 
                        $CORPORATE = stringValidation($row['cname']);
                        break;
                    case "SCHOOL":
                        $CORPORATE = stringValidation($row['cname']);
                        break;
                    case "OTHERS":
                        $CORPORATE = stringValidation($row['cname']);
                        break;
                }
                    $nature = $ewt['notify'];
                    $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue("A$index", $row['dcutdate'])
                        ->setCellValue("B$index", $row['ctin'])
                        ->setCellValue("C$index", $CORPORATE)
                        ->setCellValue("D$index", $INDIVIDUAl)
                        ->setCellValue("E$index", $ewtCode)
                        ->setCellValue("F$index", $nature)
                        ->setCellValue("G$index", $gross)
                        ->setCellValue("H$index", number_format($rate,2))
                        ->setCellValue("I$index", $credit);

                    $TOTAL_GROSS += floatval($gross); 
                    $TOTAL_CREDIT += floatval($credit); 
                    
                    $index++;
                
            }
        }
        $lastindex = $index;
        $index += 2;

        /**
         * Total Amount of Details
         */
        $spreadsheet->getActiveSheet()->getStyle("A$index:K$index")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("F$index:K$index")->getNumberFormat()->setFormatCode('###,###,###,##0.00');
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("H$index", "=SUM(H13:H$lastindex)")
        ->setCellValue("I$index", "=SUM(I13:I$lastindex)");
        // ->setCellValue("J$index", "=SUM(I$index:H$index)");

        $index += 2;
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue("A$index","END OF REPORT");
    } else {
        $spreadsheet->setActiveSheetIndex(0)
        -> setCellValue("A16", "NO RECORD");
    }


	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('SAWT');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="SAWT- Q'.$_POST['selqrtr'].' ' .$year . ' - ' . $month_text . '.xlsx"');
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
