<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	//all ewt codes used in APV
  @$arrewtcodes = array();
  $sql = "Select DISTINCT CASE WHEN A.cewtcode='' THEN '' Else A.cewtcode End as cewtcode, A.newtrate
  from 
    (
    Select compcode, cewtcode, newtrate
    From apv_d 
    where IFNULL(cewtcode,'') <> ''
  
    UNION ALL
  
    Select compcode, IFNULL(cewtcode,'') as cewtcode, newtrate
    From apv_t
    where IFNULL(cewtcode,'') <> ''
    
  ) A
  where A.compcode='$company' order by A.newtrate";

  $result=mysqli_query($con,$sql);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
    @$arrewtcodes[] = array('code' => $row['cewtcode'], 'rate' => $row['newtrate']);
  }


	//use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use \PhpOffice\PhpSpreadsheet\Cell\DataType;

	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();

	// Set document properties
	$spreadsheet->getProperties()->setCreator('Myx Financials')
			->setLastModifiedBy('Myx Financials')
			->setTitle('Monthly Input Vat and W/Tax Report')
			->setSubject('Monthly Input Vat and W/Tax Report')
			->setDescription('Monthly Input Vat and W/Tax Report, generated using Myx Financials.')
			->setKeywords('myx_financials Monthly_Ivat')
			->setCategory('Myx Financials Report');

	// Start Header
	$cols = 8;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Date');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Check No/Ref No.');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'PARTICULARS');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, 1, 'TIN');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, 1, 'Address');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, 1, 'Amount');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, 1, 'Vatable Amount');
  $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, 1, 'Input VAT');

  $arrewtamt = array();
  foreach(@$arrewtcodes as $rsx)
	{
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $rsx['code']." ".$rsx['rate']."% WTAX");
	}

	// End Headers

	// Start Details

  $dte = explode("/",$_POST["date1"]);
  $dtemo = $dte[0];
  $dteyr = $dte[1];

  $stat = $_POST["selstat"];

  $monthNum  = intval($dtemo);
  $dateObj   = DateTime::createFromFormat('!m', $monthNum);
  $monthName = $dateObj->format('F');

	$qrystat = "";
    if($stat!==""){
      $qrystat = " and A.lapproved=".$stat;
    }

    //all apv reference per check
    $arrcheckapvs = array();
    $sql = "Select CASE WHEN cpaymethod='cheque' THEN ccheckno ELSE cpayrefno End as cref, B.capvno
    From paybill A left join paybill_t B on A.compcode=B.compcode and A.ctranno=B.ctranno
    where A.compcode='$company' ". $qrystat . " and DATE_FORMAT(A.dcheckdate, '%m/%Y') = '".$_POST["date1"]."' and A.lcancelled=0";
    $resapvs=mysqli_query($con,$sql);
	
    while($row = mysqli_fetch_array($resapvs, MYSQLI_ASSOC))
    {
      $arrcheckapvs[] = array('cchkno' => $row['cref'], 'capv' => $row['capvno']);
    }


     //all apv with amt and ewt
     $arrapvdets = array();
     $sql = "Select A.ctranno, A.cewtcode, SUM(A.nvatamt) as nvatamt,  SUM(A.nnet) as nnet, SUM(A.newtamt) as newtamt
      From apv_d A left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
      where A.compcode='$company' and B.lcancelled=0 Group By A.ctranno, A.cewtcode
     
      UNION ALL

      Select A.ctranno, group_concat(A.cewtcode SEPARATOR '') as cewtcode, SUM(A.nvatamt) as nvatamt,  (B.ngross - SUM(A.nvatamt)) as nnet, SUM(A.newtamt) as newtamt
      From (
      Select A.compcode, A.ctranno, A.cacctno, IFNULL(cewtcode,'') as cewtcode,
      CASE WHEN A.cacctno = 'LIAB04005' THEN SUM(A.ndebit) Else 0 END as nvatamt,    
      CASE WHEN A.cacctno = 'LIAB04004' THEN SUM(A.ncredit) Else 0 END as newtamt
      From apv_t A 
      left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
      where A.compcode='$company' and B.captype in ('Others','PettyCash')
      Group By A.compcode, A.ctranno, A.cacctno, A.cewtcode
      ) A
      left join apv B on A.compcode=B.compcode and A.ctranno=B.ctranno
      Where B.lcancelled=0 Group by A.ctranno";
     $resapvs=mysqli_query($con,$sql);
   
     while($row = mysqli_fetch_array($resapvs, MYSQLI_ASSOC))
     {
       $arrapvdets[] = $row;
     }

    $sql = "Select CASE WHEN cpaymethod='cheque' THEN ccheckno ELSE cpayrefno End as cref, A.dcheckdate, A.ctranno, C.ctin, C.cname, C.ctradename, IFNULL(C.chouseno,'') as chouseno, IFNULL(C.ccity,'') as ccity, IFNULL(C.cstate,'') as cstate, IFNULL(C.ccountry,'') as ccountry, A.npaid
    From paybill A 
    left join suppliers C on A.compcode = C.compcode and A.ccode=C.ccode
    where A.compcode='$company' ". $qrystat . " and DATE_FORMAT(A.dcheckdate, '%m/%Y') = '".$_POST["date1"]."' and A.lcancelled=0 Order By A.dcheckdate, CASE WHEN cpaytype='cheque' THEN ccheckno ELSE cpayrefno End";

    $result=mysqli_query($con,$sql);

    $cols = 0;
    $rows = 1;

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
      $cols++;
      $rows++;

      $address = $row['chouseno'];
      if($row['ccity']!==""){
        $address  = $address. ", ". $row['ccity'];
      }
      if($row['cstate']!==""){
        $address  = $address. ", ". $row['cstate'];
      }
      if($row['ccountry']!==""){
        $address  = $address. ", ". $row['ccountry'];
      }

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['dcheckdate']);
      $cols++;
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['cref']);
      $cols++;
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['cname']);
      $cols++;
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['ctin']);
      $cols++;
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $address);


      //get apv refs
      @$apvref = array();
      foreach($arrcheckapvs as $rs2){
        if($rs2['cchkno'] == $row['cref']){
          @$apvref[] = $rs2['capv'];
        }
      }

     // print_r(@$apvref);
     // echo "<br>";

      //get values sa apv array
      $nvattot = 0;
      $nvatabletot = 0;
      foreach(@$arrewtcodes as $rsx){
        $arrewtamt[$rsx['code']] = 0;
      }
      foreach($apvref as $rs0){

        foreach($arrapvdets as $rs3){
         // echo $rs3['ctranno']."==".$rs0."<br><br>";
          if($rs3['ctranno']==$rs0){
            $nvattot = $nvattot + $rs3['nvatamt'];
            $nvatabletot = $nvatabletot + $rs3['nnet'];
            if($rs3['cewtcode']!==""){
              $arrewtamt[$rs3['cewtcode']] = $arrewtamt[$rs3['cewtcode']] + $rs3['newtamt'];
            }
          }
        }
      }

      $cols++;
      $lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['npaid']);
      $spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

      $cols++;
      $lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nvatabletot);
      $spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

      $cols++;
      $lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
      $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nvattot);
      $spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

      foreach(@$arrewtcodes as $rsx){
        $cols++;
        $lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrewtamt[$rsx['code']]);
        $spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
      }


      $cols = 0;
    }
		

	// End Details



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Monthly_IVat');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Monthly_IVat.xlsx"');
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