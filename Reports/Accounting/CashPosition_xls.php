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
	$date2 = $_POST["date2"];

    $sql = "SELECT * FROM company WHERE compcode='$company'";
    $result = mysqli_query($con, $sql);
    $comp = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$findr = array();
	$acctslist = array();
	$begbalaz = array();

	$resDR=mysqli_query($con,"Select A.ccode, A.cname, A.cacctno, B.cacctdesc, B.nbalance From bank A left join accounts B on A.compcode=B.compcode and A.cacctno=B.cacctid where A.compcode='$company' and A.cstatus='ACTIVE'");
	$findr = array();
	while($row = mysqli_fetch_array($resDR, MYSQLI_ASSOC)){
		$findr[] = $row;
		$acctslist[] = $row['cacctno'];
		//$begbalaz[$row['cacctno']] = $row['nbalance'];
	}


	$AmountTotBalance = 0;
	//for begginning balance
	
	$resBeg=mysqli_query($con,"Select A.ccode, A.cname, A.cacctno, C.cacctdesc, IFNULL(sum(B.ndebit),0) as ndebit, IFNULL(sum(B.ncredit),0) as ncredit From bank A left join glactivity B on A.compcode=B.compcode and A.cacctno=B.acctno left join accounts C on A.compcode=C.compcode and A.cacctno=C.cacctid where A.compcode='$company' and ddate < STR_TO_DATE('$date1', '%m/%d/%Y') Group BY A.ccode, A.cname, A.cacctno, C.cacctdesc");
	while($row = mysqli_fetch_array($resBeg, MYSQLI_ASSOC)){
		$begbalaz[$row['cacctno']] = floatval($row['ndebit']) -  floatval($row['ncredit']);
	}

	//for transactions
	$transbalaz = array();
	$resBeg=mysqli_query($con,"Select cmodule, ctranno, ddate, acctno, ndebit, ncredit from glactivity where compcode='$company' and ddate between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') and acctno in ('".implode("','",$acctslist)."') order by ddate, acctno");

	while($row = mysqli_fetch_array($resBeg, MYSQLI_ASSOC)){
		$transbalaz[] = $row;
	}
	
	function get_particulars($xmodule,$xtranno){
		global $con;
		global $company;

		$sql = "";

		switch($xmodule){
			case "JE":
				$sql = "Select cmemo as cparticulars from journal where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
			case "OR":
				$sql = "Select cremarks as cparticulars from receive where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
			case "APV":
				$sql = "Select cpaymentfor as cparticulars from apv where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
			case "PV":
				$sql = "Select cparticulars from paybill where compcode='$company' and lapproved=1 and ctranno='$xtranno'";
				break;
		}
		
		$cparticulars = "";
		if($sql!=""){
			$res=mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
				$cparticulars = $row['cparticulars'];
			}
		}

		return $cparticulars;
	}

// Set document properties
$spreadsheet->getProperties()->setCreator('Myx Financials')
    ->setLastModifiedBy('Myx Financials')
    ->setTitle('Cash Position Report')
    ->setSubject('Cash Position Report')
    ->setDescription('Cash Position Report, generated using Myx Financials.')
    ->setKeywords('myx_financials cash_position')
    ->setCategory('Myx Financials Report');


	$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Name of Tax Payer: ' . $comp['compdesc'])
        ->setCellValue('A2', 'Address: ' . $comp['compadd'])
        ->setCellValue('A3', 'Vat Registered Tin: ' . $comp['comptin'])
        ->setCellValue('A4', 'Kind of Book: Cash Position ')
        ->setCellValue('A5', "For the Month of $date1 to $date2");


    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A7', 'Date')
        ->setCellValue('B7', 'Reference')
        ->setCellValue('C7', 'Particulars');
    $spreadsheet->getActiveSheet()->mergeCells('A7:A9');
    $spreadsheet->getActiveSheet()->mergeCells('B7:B9');
    $spreadsheet->getActiveSheet()->mergeCells('C7:C9');


    $cols = 3;
    $rows = 7;

    $xdbalance = array();
	foreach($findr as $rocut){
		$xdbalance[$rocut['cacctno']] = 0;
        $cols++;
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rocut['cname']);
		$fromCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, $rows)->getCoordinate();

		$spreadsheet->getActiveSheet()->mergeCells($fromCellAddress.':'.$lastCellAddress);

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows+1, $rocut['cacctno']."-".$rocut['cacctdesc']);
		$fromCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows+1)->getCoordinate();
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+2, $rows+1)->getCoordinate();

		$spreadsheet->getActiveSheet()->mergeCells($fromCellAddress.':'.$lastCellAddress);


		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows+2, "Debit");
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols+1, $rows+2, "Credit");
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols+2, $rows+2, "Balance");

		$cols = $cols + 2;
    }


	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols+1, 7, "Total Balance");
	$fromCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+1, 7)->getCoordinate();
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols+1, 9)->getCoordinate();

	$spreadsheet->getActiveSheet()->mergeCells($fromCellAddress.':'.$lastCellAddress);


	//Beg Balance Row
	$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A10', '')
        ->setCellValue('B10', '')
        ->setCellValue('C10', 'Beginning Balance');
	
	$cols = 3;
    $rows = 10;
	$xtot = "";
	foreach($findr as $rocut){

		if(isset($begbalaz[$rocut['cacctno']])){
			//$xtot = floatval($begbalaz[$rocut['cacctno']]['ndebit']) - floatval($begbalaz[$rocut['cacctno']]['ncredit']);

			$xtot = $begbalaz[$rocut['cacctno']];
			$xdbalance[$rocut['cacctno']] = $xtot;
			$AmountTotBalance = $AmountTotBalance + $xtot;
		}else{
			$xtot = "0.00";
			$xdbalance[$rocut['cacctno']] = 0;
		}

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "");
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xtot);
	}

	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols+1, $rows, $AmountTotBalance);

	//Transactions
	$valdebit = 0;
	$valcredit = 0;
	$AmountTotBalance = 0;
	$cols = 1;
	foreach($transbalaz as $row)
	{
		$rows++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, date_format(date_create($row['ddate']),"m/d/Y"));
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $row['ctranno']);
		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, get_particulars($row['cmodule'],$row['ctranno']));

		foreach($findr as $rocut){
			if($row['acctno']==$rocut['cacctno']){
				$valdebit = $row['ncredit'];
				$valcredit = $row['ndebit'];
				$xdbalance[$rocut['cacctno']] = $xdbalance[$rocut['cacctno']] + floatval($row['ndebit']) - floatval($row['ncredit']);
			}else{
				$valdebit = 0;
				$valcredit = 0;
			}

			$AmountTotBalance = $AmountTotBalance + floatval($xdbalance[$rocut['cacctno']]);

			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $valdebit);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $valcredit);
			$cols++;
			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $xdbalance[$rocut['cacctno']]);
		}

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $AmountTotBalance);

		$AmountTotBalance = 0;
		$cols = 1;
	}



	// Rename worksheet
	$spreadsheet->getActiveSheet()->setTitle('Cash Position');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$spreadsheet->setActiveSheetIndex(0);

	ob_end_clean();

	// Redirect output to a client’s web browser (Xlsx)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="cash_position.xlsx"');
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