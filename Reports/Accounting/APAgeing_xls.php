<?php
	if(!isset($_SESSION)){
		session_start();
	}
	require_once  "../../vendor2/autoload.php";
	require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];

	$date1 = $_POST["date1"];

	$arrinvs = array();
	$arrsupplist = array();

	//select all Suppliers Invoice
	$sqlsuppinv = "Select A.ctranno, A.ngross, A.ndue, A.npaidamount, A.ccode, A.dreceived
	from suppinv A Where compcode='$company' and dreceived <= STR_TO_DATE('$date1', '%m/%d/%Y') and lapproved=1";
	$result=mysqli_query($con,$sqlsuppinv);

	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrinvs[] = $row;

		if(floatval($row['ndue']) > floatval($row['npaidamount']) || floatval($row['ndue']) == 0){
			$arrsupplist[] = $row['ccode'];
		}
	}

	//suppliers
	$arrsuppx = array();
	$sqlpay = "Select A.ccode, A.cname, A.cterms, IFNULL(B.nintval,0) as nintval from suppliers A left join groupings B on A.compcode=B.compcode and A.cterms=B.ccode and B.ctype='TERMS' Where A.compcode='$company' and A.ccode in ('".implode("','", $arrsupplist)."') Order by A.cname";
	$result=mysqli_query($con,$sqlpay);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrsuppx[] = array('ccode' => $row['ccode'], 'cname' => $row['cname'], 'terms' => $row['nintval']);
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
			->setTitle('AP Ageing Report')
			->setSubject('AP Ageing Report')
			->setDescription('AP Ageing Report, generated using Myx Financials.')
			->setKeywords('myx_financials ap_ageing')
			->setCategory('Myx Financials Report');

	// Start Header
	$cols = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Suppliers');

	$arrdays = array();
	$arrtotperage = array();

	$sql = "select * from ageing_days where compcode='$company' and cagetype='AP' order by id";
	$result=mysqli_query($con,$sql);
	$cntr = 0;
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$arrdays[] = $row;
		$arrtotperage[$row['id']] = 0;

		$cols++;
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, $row['cdesc']);
	}

	$cols++;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, 1, 'Grand Total');
	// End Headers

	// Start Details

	$cols = 0;
	$rows = 1;
	$gtot = 0;

	$gtotftr = 0;

	foreach($arrsuppx as $rws0){
		$cols++;
		$rows++;

		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $rws0['cname']);

		foreach($arrdays as $row){

			$cols++;

			$nmtot = 0;
			foreach($arrinvs as $xr2){
				if($xr2['ccode']==$rws0['ccode']){
					if(floatval($xr2['ndue']) > floatval($xr2['npaidamount']) || floatval($xr2['ndue']) == 0){

						$dategvn = $xr2['dreceived'];
						$cterms = $rws0['terms'];

						$your_date = date('Y-m-d', strtotime($dategvn. " + {$cterms} days"));


						$now = time(); // or your date as well $rws0['terms']
						$datediff = $now - strtotime($your_date);

						$datediff = round($datediff / (60 * 60 * 24));

						if($datediff < 0 && floatval($row['fromdays']) == 0  && floatval($row['todays']) == 0){
							$nmtot = $nmtot + floatval($xr2['ngross']);
						}else{

							if(floatval($row['fromdays']) > 0  && floatval($row['todays']) == 0){

								if($datediff >= floatval($row['fromdays'])){
	
									$nmtot = $nmtot + floatval($xr2['ngross']);
									
								}
	
							}else{
								if(($datediff >= floatval($row['fromdays'])) && ($datediff <= floatval($row['todays']))){
									//echo $datediff.": ".$xr2['ngross']."<br>";
									$nmtot = $nmtot + floatval($xr2['ngross']);
		
								}
							}

						}

									
					}
				}
			}

			$arrtotperage[$row['id']] = $arrtotperage[$row['id']] + $nmtot;

			$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();

			$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $nmtot);

			$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
			
			$gtot = $gtot + $nmtot;

		}

		$cols++;
		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $gtot);

		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");
		
		$gtotftr = $gtotftr + $gtot; 

		$gtot = 0;

		$cols = 0;

	}
	// End Details

	//TOTALS
	$rows++;
	$cols = 1;
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, "TOTAL");
	foreach($arrdays as $row){
		$cols++;

		$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
		$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $arrtotperage[$row['id']]);
		$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");

	}
	$cols++; 
	$lastCellAddress = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($cols, $rows)->getCoordinate();
	$spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($cols, $rows, $gtotftr);
	$spreadsheet->setActiveSheetIndex(0)->getStyle($lastCellAddress)->getNumberFormat()->setFormatCode("_(* #,##0.00_);_(* \(#,##0.00\);_(* \"-\"??_);_(@_)");




// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('AP_Ageing_Report');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

ob_end_clean();

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="AP_Ageing_Report.xlsx"');
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