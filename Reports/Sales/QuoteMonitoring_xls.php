<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";


	//use PhpOffice\PhpSpreadsheet\Helper\Sample;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use \PhpOffice\PhpSpreadsheet\Cell\DataType;

	// Create new Spreadsheet object
	$spreadsheet = new Spreadsheet();


	$company = $_SESSION['companyid'];
	$date1 = $_POST["date1"];
	$date2 = $_POST["date2"];
	$datefil = $_POST["seldtetp"];

	$sql = "select * From company where compcode='$company'";
	$result=mysqli_query($con,$sql);
				
	if (!mysqli_query($con, $sql)) {
		printf("Errormessage: %s\n", mysqli_error($con));
	} 
					
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$compname =  $row['compname'];
	}

	@$allqinfo = array();
	$sql = "select * From quote_t_info where compcode='$company'";
	$result=mysqli_query($con,$sql);
	if (mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			@$allqinfo[] =  $row;
		}
	}

	@$allrefx = array();
	$sql = "Select 'SI' as typ, x.creference as ctranno, GROUP_CONCAT(DISTINCT x.ctranno) as cref from sales_t x left join sales y on x.compcode=y.compcode and x.ctranno=y.ctranno where x.compcode='$company' and y.lcancelled=0 and y.lvoid=0 and IFNULL(x.creference,'') <> '' group by x.creference UNION ALL Select 'SO' as typ, x.creference as ctranno, GROUP_CONCAT(DISTINCT x.ctranno) as cref from so_t x left join so y on x.compcode=y.compcode and x.ctranno=y.ctranno where x.compcode='001' and y.lcancelled=0 and y.lvoid=0 and IFNULL(x.creference,'') <> '' group by x.creference";
	$result=mysqli_query($con,$sql);
	if (mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			@$allrefx[$row['ctranno']] =  array('typ' => $row['typ'], 'ref' => $row['cref']);
		}
	}


	// Set document properties
	$spreadsheet->getProperties()->setCreator('Myx Financials')
		->setLastModifiedBy('Myx Financials')
		->setTitle('AR Monitoring')
		->setSubject('AR Monitoring Report')
		->setDescription('AR Monitoring Report, generated using Myx Financials.')
		->setKeywords('myx_financials ar_monitoring')
		->setCategory('Myx Financials Report');

	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A1', strtoupper($compname))
		->setCellValue('A2', 'QUOTATION MONITORING')
		->setCellValue('A3', 'For the Period '.date_format(date_create($_POST["date1"]),"F d, Y")." to ".date_format(date_create($_POST["date2"]),"F d, Y"));

	$spreadsheet->getActiveSheet()->mergeCells("A1:I1");
	$spreadsheet->getActiveSheet()->mergeCells("A2:I2");
	$spreadsheet->getActiveSheet()->mergeCells("A3:I3");

	$postedtran = $_POST["selrpt"];

	$mainqry = "";
	$finarray = array();

	$qryposted = "";
	if($postedtran==1 || $postedtran==0){
		$qryposted = " and B.lcancelled=0 and B.lvoid=0 and B.lapproved=".$postedtran."";
	}elseif($postedtran==2){
		$qryposted = " and (B.lcancelled=1 or B.lvoid=1)";
	}


	$transctions = array();
	$sqlx = "Select B.*, C.cname
	From quote B
	left join customers C on B.compcode=C.compcode and B.ccode=C.cempid  
	where B.compcode='$company' and date(B.".$datefil.") between STR_TO_DATE('$date1', '%m/%d/%Y') and STR_TO_DATE('$date2', '%m/%d/%Y') ".$qryposted." Order by B.dcutdate, B.ctranno";

	$result=mysqli_query($con,$sqlx);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$finarray[] = $row;
		$transctions[] = $row['ctranno'];
	}

	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A5', 'BILLING');
	$spreadsheet->getActiveSheet()->mergeCells("A5:I5");

	// Add some data
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A6', 'Transaction No.')
		->setCellValue('B6', 'Billing Date')
		->setCellValue('C6', 'Due Date')
		->setCellValue('D6', 'Customer')
		->setCellValue('E6', '')
		->setCellValue('F6', 'Recurr')
		->setCellValue('G6', 'Sales Type')
		->setCellValue('H6', 'VAT Type')
		->setCellValue('I6', 'Total Amount')
		->setCellValue('J6', 'Status');

	$spreadsheet->getActiveSheet()->mergeCells("D6:E6");
	$spreadsheet->getActiveSheet()->getStyle('A6:J6')->getFont()->setBold(true);

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;
	$cnt = 6;

	$totbills = 0;
	foreach($finarray as $row)
	{
		if($row['quotetype']=="billing"){
			if(@$allrefx[$row['ctranno']]['ref']=="" || @$allrefx[$row['ctranno']]['ref']==null){
			$cnt++;

			if($row['lcancelled']==1 || $row['lvoid']==1){
				if($row['lcancelled']==1){
					$xycolor = "Cancelled";
				}
	
				if($row['lvoid']==1){
					$xycolor = "Void";
				}
				
			}else{
				if($row['lapproved']==1){
					$xycolor = "Posted";
				}else{
					$xycolor = "Pending";
				}
			}

			$totbills += floatval($row['ngross']);

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['ctranno'])
			->setCellValue('B'.$cnt, date_format(date_create($row['dtrandate']),"m/d/Y"))
			->setCellValue('C'.$cnt, date_format(date_create($row['dcutdate']),"m/d/Y"))
			->setCellValue('D'.$cnt, $row['ccode'])
			->setCellValue('E'.$cnt, $row['cname'])
			->setCellValue('F'.$cnt, strtoupper($row['crecurrtype']))
			->setCellValue('G'.$cnt, $row['csalestype'])
			->setCellValue('H'.$cnt, $row['cvattype'])
			->setCellValue('I'.$cnt, $row['ngross'])
			->setCellValue('J'.$cnt, $xycolor);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");		
			
		}
		}
	}

	$cnt++;

	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$cnt, 'Total')
	->setCellValue('I'.$cnt, $totbills);
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":H".$cnt);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('I'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":J".$cnt)->getFont()->setBold(true);

	$cnt++;
	$cnt++;

	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$cnt, 'QUOTATIONS');
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":I".$cnt);

	// Add some data
	$cnt++;
	$spreadsheet->setActiveSheetIndex(0)
		->setCellValue('A'.$cnt, 'Transaction No.')
		->setCellValue('B'.$cnt, 'Quote Date')
		->setCellValue('C'.$cnt, 'Effectivity Date')
		->setCellValue('D'.$cnt, 'Customer')
		->setCellValue('E'.$cnt, '')
		->setCellValue('F'.$cnt, 'Sales Type')
		->setCellValue('G'.$cnt, 'VAT Type')
		->setCellValue('H'.$cnt, 'Total Amount')
		->setCellValue('I'.$cnt, 'Status');

	$spreadsheet->getActiveSheet()->mergeCells("D".$cnt.":E".$cnt);
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":I".$cnt)->getFont()->setBold(true);

	$salesno = "";
	$remarks = "";
	$invval = "";
	$code = "";
	$name= "";
	$dateval="";
	$classcode="";
	$totAmount=0;	
	$ngross = 0;

	$totbills = 0;
	foreach($finarray as $row)
	{
		if($row['quotetype']=="quote"){
			if(@$allrefx[$row['ctranno']]['ref']=="" || @$allrefx[$row['ctranno']]['ref']==null){
			$cnt++;

			if($row['lcancelled']==1 || $row['lvoid']==1){
				if($row['lcancelled']==1){
					$xycolor = "Cancelled";
				}
	
				if($row['lvoid']==1){
					$xycolor = "Void";
				}
				
			}else{
				if($row['lapproved']==1){
					$xycolor = "Posted";
				}else{
					$xycolor = "Pending";
				}
			}

			$totbills += floatval($row['ngross']);

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$cnt, $row['ctranno'])
			->setCellValue('B'.$cnt, date_format(date_create($row['dtrandate']),"m/d/Y"))
			->setCellValue('D'.$cnt, $row['ccode'])
			->setCellValue('E'.$cnt, $row['cname'])
			->setCellValue('F'.$cnt, $row['csalestype'])
			->setCellValue('G'.$cnt, $row['cvattype'])
			->setCellValue('H'.$cnt, $row['ngross'])
			->setCellValue('I'.$cnt, $xycolor);

			$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");		
		}	
		}
	}	

	$cnt++;

	$spreadsheet->setActiveSheetIndex(0)
	->setCellValue('A'.$cnt, 'Total')
	->setCellValue('H'.$cnt, $totbills);
	$spreadsheet->getActiveSheet()->mergeCells("A".$cnt.":G".$cnt);
	$spreadsheet->setActiveSheetIndex(0)->getStyle('H'.$cnt)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");	
	$spreadsheet->getActiveSheet()->getStyle("A".$cnt.":H".$cnt)->getFont()->setBold(true);


// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('ARMonitoring');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ARMonitoring.xlsx"');
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